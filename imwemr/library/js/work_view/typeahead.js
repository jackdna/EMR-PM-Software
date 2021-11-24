//--Typeahead --

function show_staging_link(id){
	var ass_id="";
	var show_staging="";
	if(id.indexOf("elem_assessment_dxcode")!=-1){
		ass_id=id;
	}else if(id.indexOf("elem_assessment")!=-1){
		ass_id=id.replace("elem_assessment","elem_assessment_dxcode");
		if($("#"+ass_id).val()==""){
			ass_id=id.replace("elem_assessment_dxcode","elem_assessment");
		}
	}else if(id.indexOf("elem_dxCode")!=-1){
		ass_id=id;
	}

	if(ass_id!=""){
		var ass_val = ($("#"+ass_id).val()).split('.');
		if($.trim(ass_val[0])=='H40' || $.trim(ass_val[0])=='h40'){
			show_staging=$("#"+ass_id).val();
		}else{
			ass_val = (ass_val[0]).split(':');
			if($.trim(ass_val[1])=='H40' || $.trim(ass_val[1])=='h40'){
				show_staging=$("#"+ass_id).val();
			}
		}
	}
	return show_staging;
}

function cn_isFulldxAdded(ob){

	var fldid04 = ""+ob.id;
	var dxdb = $("#"+fldid04).data("valbakdb");
	var dxdsc = $("#"+fldid04).data("valdxdesc");
	var st1srch = ""+$("#"+fldid04).data("val1stsrch");
	var srchval = ""+$("#"+fldid04).data("valsrchd").toUpperCase().trim();
	var ar_st1srch = st1srch.split(",");
	st1srch = $.trim(ar_st1srch[ar_st1srch.length-1]);
	st1srch = st1srch.toUpperCase().trim();



	//remove attributes
	$("#"+fldid04).removeData( "val1stsrch" );

	if(typeof(dxdb)!="undefined" && typeof(dxdsc)!="undefined"){
		if(""+st1srch != ""+srchval && srchval.indexOf("-")==-1){ //user has entered full code
			//console.log($("#"+fldid04).data("val1stsrch"), $("#"+fldid04).data("valbakdb"), $("#"+fldid04).data("valdxdesc"));
			if(ob.id.indexOf("elem_assessment")!=-1 && ob.id.indexOf("_dxcode")==-1){ //a
				ob.value=dxdsc;
				$(ob).triggerHandler("blur");$(ob).triggerHandler("change");
				var dx_id = ob.id.replace("elem_assessment","elem_assessment_dxcode");
				$("#"+dx_id).val(srchval);
			}else if(ob.id.indexOf("elem_assessment_dxcode")!=-1){ //d
				ob.value=srchval;
				var as_id = ob.id.replace("elem_assessment_dxcode","elem_assessment");
				if($.trim($("#"+as_id).val())=="" && $("#"+as_id).length>0){$("#"+as_id).val(dxdsc); $("#"+as_id).triggerHandler("blur"); $("#"+as_id).triggerHandler("change"); } //
			}else{
				ob.value=srchval;
			}

			return true;
		}
	}
	return false;
}

function getIEtextareaCaret(el) {

	var start = 0;
	if(typeof(el.createTextRange)=="function" && document.selection && typeof(document.selection.createRange())!="undefined"){
		var range = el.createTextRange(),
		range2 = document.selection.createRange().duplicate(),
		// get the opaque string
		range2Bookmark = range2.getBookmark();

		// move the current range to the duplicate range
		range.moveToBookmark(range2Bookmark);

		// counts how many units moved (range calculated as before char and after char, loop count is the position)
		while (range.moveStart('character' , -1) !== 0) {
			start++;
		}

	}else if(el.selectionStart){
		var val = el.value;
		start =val.slice(0, el.selectionStart).length;
	}else{
		start = el.length;
	}

	if(typeof(start)=="undefined"){  start=0; }

	return start;
}

function jq_extractLast( term , side, o) {
    //return jq_split( term ).pop();

	var term_full=term;
	var cat = getIEtextareaCaret(o) ;

	term = term.substring(0, cat);

	var n0=term.lastIndexOf("\n");
	term=$.trim(term);


	//var n1=(n0==-1) ? term.lastIndexOf(";") : -1 ;
	//var n2=(n0==-1) ? term.lastIndexOf(",") : -1 ;

	var ret ="", ret2="";
	//if(n0>n1 && n0>n2){
		if(side==1){
			ret = ""+$.trim(term.substring(n0+1));
		}else if(side==2){
			ret = ""+$.trim(term.substring(0,n0))+"\n";
			srch = ""+$.trim(term.substring(n0+1));
			ret2 = ""+$.trim(term_full.substring(n0))+"";
			cat=cat-srch.length;

		}
	//}
	/*else if(n1>n2 && n1>n0){
		if(side==1){
			ret = ""+$.trim(term.substring(n1+1));
		}else if(side==2){
			ret = ""+$.trim(term.substring(0,n1))+"; ";
			srch = ""+$.trim(term.substring(n0+1));
			ret2 = ""+$.trim(term_full.substring(n0))+"";
			cat=cat-srch.length;
		}
	}else{
		if(side==1){
			ret = ""+$.trim(term.substring(n2+1));
		}else if(side==2){
			var ret= ""+$.trim(term.substring(0,n2));
			srch = ""+$.trim(term.substring(n0+1));
			if(n2!=-1){
				ret+=", ";
			}
			ret2 = ""+$.trim(term_full.substring(n0))+"";
			cat=cat-srch.length;
		}
	}*/

	if(ret2.length>0){  ret2 = ret2.replace(srch,"");  }

	if(cat<0){cat=0;}

	//if(side==2)
	//alert("ret: "+ret+", ret2:"+ret2);

	return (side==2) ? [ret,ret2, cat,srch] : ret;
}

function jq_getStarter(term){
	var r="";
	var o = new RegExp("^(\\-|((\\d+|\\w)\\.))", "i");
	if(o.test(term)){
		r=o.exec(term);
		r=r[0];
		term=term.replace(o,"");
		term=$.trim(term);
	}
	return [r,term];
}

function cn_rem_escp_char(s){/*r*/
	if(s!=""){
	s = s.replace(/\\'/g,"'"); //stripslashes
	s = s.replace(/\\&/g,"&"); //stripamp
	}
	return s;
}

function cn_adjustSearchValue(hystck, ndl){
	var ar_ndl = ndl.split(" ");
	var last_ndl="", flgFull=0;
	if(ar_ndl.length > 1){
		last_ndl=ar_ndl[ar_ndl.length-1];
		// --
		/*
		var last_ndl_2 = ar_ndl[ar_ndl.length-2];
		if(last_ndl_2!="" ){ //&& (last_ndl_2.indexOf(",") != -1 || last_ndl_2.indexOf(";") != -1)
			flgFull=1;
		}*/
	}

	//alert("flgFull: "+flgFull+"\n\n"+hystck.indexOf(ndl)+"\n\nndl; "+ndl+"\n\nhystck; "+hystck+"\n\nlast_ndl_2:"+last_ndl_2+"\n\nlast_ndl:"+last_ndl);
	//dry dry eyes issue fixed
	if(hystck.toLowerCase().indexOf(ndl.toLowerCase())!=-1 || flgFull==1){
		//alert("1\n"+hystck);
		return hystck;
	}else{

		//window.status = last_ndl;
		var tmp="";
		if(last_ndl!="" && hystck.toLowerCase().indexOf(last_ndl.toLowerCase())!=-1){
			ar_ndl.length = ar_ndl.length-1;
			var tmp = ar_ndl.join(" ");
			if($.trim(tmp)!=""){ tmp=tmp+" "; }
		}
		//alert("2\n"+tmp+hystck);
		return tmp+hystck;
	}
}
// check last term is lesser than 2
var chk_term_length = function(term){
		var art = term.split(" "), art_ln = art.length;
		if(art_ln>0){
			var t = $.trim(art[art_ln-1]);
			if(t.length<3){ return false; }
		}
		return true;
	};


var cn_ta1_no_assess = function(ota1, strExamName){
	if(typeof(strExamName)=="undefined"){ strExamName=""; }
	$( ota1 ).autocomplete({
		//open: function(event, ui){ if(ota1 && typeof(ota1.id)!="undefined"&&ota1.id.indexOf("elem_plan")!=-1){$(ota1).autocomplete ("widget").css("width",parseInt($(ota1).width())+"px");}}, //set width
	    //source: "common/requestHandler.php?elem_formAction=TypeAhead",
		source: function( request, response ) {
			//if MR comment box then show console AP
			var mode="";
			if(this.element[0].name&&typeof(this.element[0].name)!="undefined"&&
				(this.element[0].name=="elem_visMrDesc" || this.element[0].name.indexOf("elem_visMrDescOther")!=-1 )){
				var tmpICD10 = $("#hid_icd10").val();
				tmpICD10 = (tmpICD10==1) ? 1 : 0;
				mode="&mode=MR&ICD10="+tmpICD10;
			}
			/*
            //Procedure SuperBill
			if(strExamName === '&exmnm=Procedures') {
                if($(this.element.get(0)).hasClass('dxallcodes')) {
                    mode="&mode=getICD10Data";
                }
                if($(this.element.get(0)).hasClass('cptcode')) {
                    mode="&mode=cptdropdown";
                }
                if($(this.element.get(0)).hasClass('modcode')) {
                    mode="&mode=moddropdown";
                }
            }*/
		var str_prfx = "";
		if(typeof(top.JS_WEB_ROOT_PATH)!="undefined"){ // &&
			str_prfx = top.JS_WEB_ROOT_PATH+"/interface/chart_notes/";
		}

		    $.getJSON( str_prfx+"requestHandler.php?elem_formAction=TypeAhead"+strExamName+mode, {
			term: jq_extractLast( request.term ,1,this.element[0])
		    }, response );
		},
		search: function() {
		    // custom minLength
		    var term = jq_extractLast( this.value ,1, this);
		    var o = jq_getStarter(term);
		    this.strter=o[0]; //

			// check last term is lesser than 3
			return chk_term_length(term);

		    //if ( o[1].length < 2 ) {
			//return false;
		    //}
		},
	    minLength: 3,
	    autoFocus: true,
	    focus: function( event, ui ) {return false;},
	    select: function( event, ui ) {
		    if(ui.item.value!=""){

			    arr = ui.item.value.split("~~");
			    ui.item.value=arr[0];

			    var t = jq_extractLast( this.value ,2, this);
			    var r = ""+ui.item.value;
			    r=cn_adjustSearchValue(r,t[3]);

			    if(this.strter!=""){r=$.trim(this.strter+" "+r);}


			    this.value =  $.trim(""+t[0]+""+r+t[1]);

			     if(typeof(setCursorAtEnd)!="undefined"){ setCursorAtEnd(this,t[2]+r.length); }

			    $(this).triggerHandler("change");
			    $(this).triggerHandler("blur");
			    $(this).triggerHandler("click");

			    //


			//$( this ).autocomplete( "close" );
			return false;/*this.value=""+ui.item.value;$(this).trigger("keyup");*/} }
	    //change: function( event, ui ) { if(ui.item.value!=""){$(this).triggerHandler("change").triggerHandler("blur");/*this.value=""+ui.item.value;$(this).trigger("keyup");*/ }}
	}).filter("textarea[id^=elem_plan]").each(function(){$(this).triggerHandler('keyup');});
};

function cn_typeahead_order(){

	var ta3 = function(ota3){
		$( ota3 ).autocomplete({
		    //source: "common/requestHandler.php?elem_formAction=TypeAhead",
			source: zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=getorder&req_ptwo=1",
		    minLength: 2,
		    autoFocus: true,
		    focus: function( event, ui ) {return false;},
		    select: function( event, ui ) { if(ui.item.value!=""){
				var r = ""+ui.item.value;
				this.value =  $.trim(""+r);
				$(this).parent().find(":hidden[id*=med_id]").val( ui.item.id );
				//$( "#med_id" ).val( ui.item.id );
				$(this).triggerHandler("change");
				$(this).triggerHandler("blur");
				$(this).triggerHandler("click");
				return false;
				} }
		}); //.each(function(){$(this).triggerHandler('change');})
	};
	$( "input[type=text][id*=ele_order_name]"  ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta3(this);};});

}

function cn_ta_cvf_ag(ptrn){
	//Not assess
	$( ptrn ).bind("focus", function(){ if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, "");};  });
}

function cn_typeahead(flg){

	var browser = navigator.appName;
	var b_version = navigator.appVersion;
	var version = parseFloat(b_version);

	//Lens Used
	if(typeof(rv_lens_used)!="undefined"){rv_lens_used();}

	//---
	var strExamName="";
	var ptrntextbox=""; //allow type ahead in textboxes in exam pop ups
	if(typeof(examName)!="undefined" && (examName!="")){
		strExamName = "&exmnm="+examName;
		ptrntextbox=",input[name][type=text]:not(.mulPressure input[name][type=text])";
	}
	//
	//---

	if(version!=4){
		//Not assess
	$( "textarea[name]:not(textarea[name^='elem_assessment'])"+ptrntextbox ).bind("focus", function(){ if(!$(this).hasClass("ui-autocomplete-input")){cn_ta1_no_assess(this, strExamName);};  });
	//.filter(function(){ return (this.id && (this.id.indexOf("elem_plan")!=-1||this.id.indexOf("commentsForPatient")!=-1 || this.id.indexOf("elem_consult_reason")!=-1 || this.id.indexOf("elem_visPcDesc")!=-1 || this.id.indexOf("elem_visMrDesc")!=-1 || this.id.indexOf("elem_notes")!=-1)) ? true : false; }).each(function(){$(this).triggerHandler('keyup');});
	$("#assessplan .col-sm-12-pl textarea[name^='elem_plan'], #commentsForPatient, #elem_consult_reason, #PC  textarea[name^='elem_visPcDesc'], #MR  textarea[name^='elem_visMrDesc'], #elem_notes").each(function(){$(this).triggerHandler('keyup');});

	//if exam then return
	//if(typeof(examName)!="undefined" && examName!=""){return;}

		getDataFilePath = 'requestHandler.php?elem_formAction=TypeAhead&mode=assessment&callFrom=wv';

		var flg_LitSev = "";
		var ta2 = function(ota2){
		//start function--
			$( ota2 ).bind( "keydown", function(event){
		}).autocomplete({
		open: function(event, ui){  $(ota2).autocomplete ("widget").css("width",parseInt($(window).width()*3/4)+"px"); },
		source: function(request,response){// delegate back to autocomplete, but extract the last term
			showMode = "ap";
			assessment = uV = request.term;//UI selected value
			vARR 	= uV.split('[ICD-10: ');
			if(vARR.length>1){
				ICD10_ARR	= vARR[1].split(',');
				if(ICD10_ARR.length>1){
					ICD10	= ICD10_ARR[0];
					request.term = ICD10;
					assessment = vARR[0];
				}
			}else{
				delete ICD10;
			}
			//$('#temp_txt').val(request.term);
			request.term = request.term.replace(/^\s|\s$/g,'');

			//

			var term = jq_extractLast( request.term ,1,this.element[0]);
			var o = jq_getStarter(term);
			this.element[0].strter=o[0]; //

			/*
			if ( o[1].length < 2 ) {
				return false;
			}*/

			flg_LitSev=0;

			//exam_name
			if(typeof(examName)!="undefined" && examName=="Fundus"){
				var tmpICD10 = window.opener.top.fmain.$("#hid_icd10").val();
				tmpICD10 = (tmpICD10==1) ? 1 : 0;

				var chart_dos="";
				if(typeof(window.opener.top.fmain.$("#elem_dos").val())!="undefined" && $("textarea[name^='elem_assessment']")){
					chart_dos=window.opener.top.fmain.$("#elem_dos").val();
				}
			}else{
				var tmpICD10 = $("#hid_icd10").val();
				tmpICD10 = (tmpICD10==1) ? 1 : 0;

				var chart_dos="";
				if(typeof($("#elem_dos").val())!="undefined" && $("textarea[name^='elem_assessment']")){
					chart_dos=$("#elem_dos").val();
				}
			}

			if(getDataFilePath == "../common/getICD10data.php"){
				//getDataFilePath = "../common/getICD10data.php?term="+term+"&ICD10="+tmpICD10+"&show_pop=1";
				getDataFilePath = "requestHandler.php?elem_formAction=TypeAhead&mode=getICD10Data&term="+term+"&ICD10="+tmpICD10+"&show_pop=1";
			}else{
				getDataFilePath = getDataFilePath+'&term='+term+'&ICD10='+tmpICD10+'&show_pop=1&chart_dos='+chart_dos;
			}

			var oe = this.element[0];
			/*
			var tmp_pos = parseInt(""+term.lastIndexOf('-')) + 1;
			if(term.length>3 && term.length<=10){
				if(term.length==tmp_pos || term.charAt(6) == "-"){
					flg_LitSev=1;
				}
			}
			*/


			//alert(getDataFilePath);

			//a=window.open();a.document.write(getDataFilePath);

			//getDataFilePath = "requestHandler.php?elem_formAction=TypeAhead&mode=getICD10Data&term=H40.21-&ICD10=1&show_pop=1";

			//set value when search starts
			if(typeof($(oe).data("val1stsrch"))=="undefined"){
				$(oe).data("val1stsrch",term);
			}

			$.ajax({
				url: getDataFilePath,
				dataType: "json",
				success: function(availableTags){	//b=window.open();b.document.write(typeof(availableTags)+"\n\r"+availableTags);

					//var oalert = window.open("","dd","width=200,height=200,resizable=1");
					//oalert.document.write(""+availableTags);

					//alert(""+availableTags);
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


					if(vARR && vARR.length>1){request.term = '';}request.term = '';
					if(flg_LitSev==1){
						//alert(""+$.ui.autocomplete.filter(availableTags,icd10_extractLast(request.term)));
						//var oalert1 = window.open("","dd1","width=200,height=200,resizable=1");

						/*
						var rr = "";
						for(var x in availableTags){ //ui['content'][1]
							rr+=x+"-"+availableTags[x]+"<br>";
						}

						oalert1.document.write(typeof(availableTags)+"\n\r"+rr);

						var rr=[];
						//LTR
						if(availableTags['LTR'] && typeof(availableTags['LTR'])!="undefined" && availableTags['LTR'].length>0){
							rr['LTR'] = availableTags['LTR'];

						}

						//SVR
						if(availableTags['SVR'] && typeof(availableTags['SVR'])!="undefined" && availableTags['SVR'].length>0){
							rr['SVR'] = availableTags['SVR'];
						}


						//*/

						//alert("NN");

						response(availableTags);
					}else{
						//alert("333");
						response($.ui.autocomplete.filter(availableTags,icd10_extractLast(request.term)));
					}
				}
			});
		},
		/*search: function(event, ui){
			var term = jq_extractLast( this.value ,1, this);
			// check last term is lesser than 2
			return chk_term_length(term);
		},*/
		response: function( event, ui){

			//var flg_LitSev = $(this).data( "flg_LitSev");
			//*
			//alert(flg_LitSev);
			if(flg_LitSev){


				//console.log("1");
				if(cn_isFulldxAdded(this)){
					//
				}else{ //dx code is not full added

				//remove attributes
				$("#"+this.id).removeData( "val1stsrch" );

				if($("#dialogLSS").length>0){
					if($("#pop_ass_flag").length>0){
						var fldid01 = ""+this.id;
						var fldid02=fldid01.replace("pop_","");
						if(fldid02.indexOf("elem_assessment_dxcode") != -1 || fldid02.indexOf("elem_assessment") != -1){
							var fldid03 = fldid02.replace("elem_assessment","elem_assessment_dxcode");
							var elem_ass_comm=$("#elem_ass_comm").val();
							var fldid03_exp = $("#"+fldid01).val().split('[ICD-10: ');
							var fldid03_arr	="";
							if(fldid03_exp.length>1){
								fldid03_arr	= fldid03_exp[1].split(',');
							}
							if(elem_ass_comm!="" && typeof(elem_ass_comm)!="undefined"){
								$("#"+fldid02).val($("#"+fldid01).val()+";;"+elem_ass_comm);
							}else{
								$("#"+fldid02).val($("#"+fldid01).val());
							}
							$("#"+fldid03).val(fldid03_arr[0]);
							$("#"+fldid03).data("valbakdb",fldid03_arr[0]);
							$("#dialogLSS").remove();
							$("#"+fldid02).triggerHandler("click");
							$("#"+fldid02).triggerHandler("blur");
							$("#"+fldid02).triggerHandler("change");
						}else if(fldid02.indexOf("elem_dxCode") != -1){
							var fldid03_exp = $("#"+fldid01).val().split('[ICD-10: ');
							var fldid03_arr	="";

							if(fldid03_exp.length>1){
								fldid03_arr	= fldid03_exp[1].split(',');
							}else{
								fldid03_arr	= fldid03_exp[1];
							}
							$("#"+fldid02).val(fldid03_arr[0]);
							$("#"+fldid02).data("valbakdb",fldid03_arr[0]);
							$("#dialogLSS").remove();
							$("#"+fldid02).triggerHandler("click");
							$("#"+fldid02).triggerHandler("blur");
							$("#"+fldid02).triggerHandler("change");
						}
					}else{
						$("#dialogLSS").remove();
					}

				}
				//*

				//var oalert = window.open("","dd","width=200,height=200,resizable=1");

				//var rr ="";
				//for(var x in ui){ //ui['content'][1]
				//	rr+=x+"-"+ui[x]+"<br>";
				//}

				//oalert.document.write("<xmp>"+" - "+rr+"</xmp>");


				var rr="";
				var i=1;
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
						fldid = fldid.replace("pop_","");
						//alert(fldid);
						//alert(tmp);
						if(i==0){ lat_ct_show="txt_lat"; }else if(i==1){ lat_ct_show="txt_sev";  }else if(i==2){ lat_ct_show="txt_stag";  }
						ee += "<span id='txt_span_latt' onclick=\"cn_resetasval('"+fldid+"', '"+showval+"','"+i+"', this);\"></span>";
						if(i==0){
							if(showtxt.toLowerCase().indexOf("lid")!=-1){
								if(showtxt.toLowerCase().indexOf("right")!=-1){
									ee += "<tr><td  id='"+lat_ct_show+"_"+lat_ct+"' dbid='"+showval+"' onclick=\"cn_resetasval('"+fldid+"', '"+showval+"','"+i+"', this);\">"+showtxt+"</td></tr>";//
								}else{
									ee1 += "<tr><td  id='"+lat_ct_show+"_"+lat_ct+"' dbid='"+showval+"' onclick=\"cn_resetasval('"+fldid+"', '"+showval+"','"+i+"', this);\">"+showtxt+"</td></tr>";//
								}
								continue;
							}
						}

						ee += "<tr><td id='"+lat_ct_show+"_"+lat_ct+"' dbid='"+showval+"' onclick=\"cn_resetasval('"+fldid+"', '"+showval+"','"+i+"', this);\">"+showtxt+"</td></tr>";//

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

					if(typeof(js_icd_data_arr)!="undefined" && js_icd_data_arr &&typeof(js_icd_data_arr[final_dx_val])!="undefined"){
						for(var k=0;k<js_icd_data_arr[final_dx_val].length;k++){
							js_icd_data_str+=js_icd_data_arr[final_dx_val][k];
						}
					}

					if(js_icd_data_str!=""){
						js_icd_data_ee="<table id='LSk3'><tr><td style='padding:0px 0px 4px 0px;'><input type='hidden' id='fldid_chk' name='fldid_chk' value='"+fldid+"' ><b>Causative Factors</b></td></tr>"+js_icd_data_str+"</table>";
					}
					var obj_comm_div="";
					if(fldid.indexOf("elem_assessment")!=-1){
						var fldid_comm_id = fldid.replace("_dxcode","");
						var obj_comm_exp = ($("#"+fldid_comm_id).val()).split(';');
						var obj_comm_val="";
						if(typeof(obj_comm_exp[2])!="undefined"){
							for(l=1;l<=obj_comm_exp.length;l++){
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
					}else{
						var show_ass_id = fldid.replace("pop_","");
					}
					var show_ass_val = ($("#"+show_ass_id).val()).split('[');

					rr = "<div class=\"dvLss\"><table id=\"bigtbl\" border='0' width='100%'><tr valign='top'>"+rr+"</tr></table></div>";
					var dd = "<div id=\"dialogLSS\" title=\"Select ICD 10 Dx code\">"+
							"<input type=\"hidden\" id=\"pop_ass_flag\" value=\"1\" ><textarea class=\"input_text_10\" id='pop_"+show_ass_id+"' name='pop_elem_assessment[]' style=\"width:410px;height:20px;\">"+show_ass_val[0]+"</textarea>"+
							rr+"<input type=\"hidden\" id=\"olen\" value=\"1\" >"+
							js_icd_data_ee+obj_comm_div+"</div>";

					var del_btn_val =  "Delete Dx code";
					if(fldid.indexOf("elem_assessment_dxcode")!=-1 || fldid.indexOf("elem_assessment")!=-1){
						del_btn_val = "Delete Assessment and Dx code";
					}
					if(js_icd_data_ee!=""){
						var dialogLSS_width='700';
					}else{
						var dialogLSS_width='450';
					}
					var show_staging_link_val=show_staging_link(show_ass_id);
					//alert(dd);
					$("body").append(dd);
					$( "#dialogLSS" ).dialog({
						close : function(){
							if($("#elem_ass_comm")){
								$("#elem_ass_comm").val('');
							}
						},buttons: [/*{
										text : ""+del_btn_val,
										click : function() {
										  $( this ).dialog( "close" );
										  if(fldid.indexOf("elem_assessment_dxcode")!=-1){
											  var fldid_tmp = fldid.replace("elem_assessment_dxcode","elem_assessment");
											  $("#"+fldid).val("");
											  $("#"+fldid_tmp).val("");
										  }
										}
										},*/
										{
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
										},
										{
										id: "dmb_reset",
										text : "Reset",
										click : function() {
											reset_assessment(fldid);
										}
										},
										{
										id: "dmb_staging_code",
										text : "Staging Code",
										click : function() {
											staging_div_view('show');
										}
										},
										{
										id : "dialog_add_mutli_dx",
										text : "Add Multiple Dx",
										click : function() {
										  //$( this ).dialog( "close" );

										  $("#dialogLSS").append("");

										  $("#dialogLSS div.dvLss table#bigtbl").clone().appendTo("#dialogLSS div.dvLss");
											//.
										 // alert("3: "+$("#dialogLSS table#bigtbl").length);

										  var len = $("#dialogLSS table[id*=bigtbl]").length;
										  var inx = len - 1;
										  var oo = $("#dialogLSS table[id*=bigtbl]").get(inx);
											oo.id = "bigtbl"+inx;
											//alert(oo.id);
											$("#dialogLSS #olen").val(len);
										  //$("#dialogLSS table#bigtbl").css({"background-color":"red;"});

										}
										}
										]
									      ,minWidth: dialogLSS_width});
									      $("#dmb_reset").css({"float":"right"});
										  if(show_staging_link_val==""){
											  $("#dmb_staging_code").hide();
										  }
										  $("#dmb_staging_code").css({"float":"left","background":"none","color":"purple","border":"none","font-weight":"bold"});
					//
					$("#dialog_add_mutli_dx").hide();
					cn_typeahead();
					//$("#elem_ass_comm").blur();
					if($("#dialog_add_mutli_dx").length>0){
						if(fldid.indexOf("elem_assessment")!=-1){
							var fldid_as = fldid.replace(/elem_assessment/,"elem_assessment_dxcode");
							var tt = $("#"+fldid_as).val();
							var artt = tt.split(",");
							var lentt=artt.length;
							for(var t1=1;t1<lentt;t1++){
								$("#dialog_add_mutli_dx").trigger("click");
							}
						}
					}
				}
				//*/
				}//else end
				//console.log("jQuery is not loaded");
				$( this ).autocomplete( "close" );
				//alert("22");
			}
			return false;
			//*/
		},
		minLength: 1,
		focus: function(){// prevent value inserted on focuS
			return false;
		},
		close : function(){

			if((typeof(ICD10) == 'string' && ICD10.substr((ICD10.length-1),1)=='-') || ($(this).val().indexOf('>>') > 0)){
				getDataFilePath = '../common/getICD10data.php';

				if(hiddenOBJ.val()=="" || ""+hiddenOBJ.val().indexOf("-")!=-1)
				$(this).autocomplete("search");
			}else{
				getDataFilePath = 'requestHandler.php?elem_formAction=TypeAhead&mode=assessment&callFrom=wv';
			}
		},
		select: function(event,ui){
			var flgInWv = (typeof(examName)!="undefined" && examName=="Fundus") ? 0 : 1;
			if(flgInWv){ getForthAssess(); }
			arr = ui.item.value.split("~~");
			index = $(this).attr('id').match(/\d+$/);
			if($(this).attr('id').indexOf("elem_assessment_dxcode") != -1 ||  $(this).attr('id').indexOf("elem_assessment") != -1){
				hiddenOBJ = $("#elem_assessment_dxcode"+index);
			}else{
				hiddenOBJ = $("#elem_dxCode_"+index);
			}

			if(this.value.indexOf(";")==-1){
				if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){hiddenOBJ.val(''); hiddenOBJ.data("dxid", ""); }
			}
			//
			//$(this).data("dxid", "");

			if(arr[1] == 'commu' || arr[1] == 'phy' || arr[1] == 'dyn' || arr[1] =='dx'){

					//alert(this.value);

					var tmp_val="";
					ui.item.value = arr[0];
					var tmp_ival = cn_rem_escp_char(ui.item.value); //ui.item.value.replace(/\\'/g,"'"); //stripslashes
					uV1		= tmp_ival;//UI selected value

					if(uV1.slice(-1) == ")" && uV1.indexOf(" \t\r(")!=-1){
						arr1 = uV1.split(/\([^\(]*$/);
						//this.value = arr1[0];
						tmp_val = arr1[0];
						if(this.value.indexOf(";")==-1||hiddenOBJ.val()==""){
							var tmp_dx_id = (typeof(ui.item.dxid)!="undefined") ? ui.item.dxid : "" ;
							vARR1 	= uV1.split("(").pop().replace(/\)$/g,'');
							if(typeof(asmt_sep_multi_dx_code)=="undefined" || flgInWv==0){ asmt_sep_multi_dx_code=0; }
							if(asmt_sep_multi_dx_code==1 && typeof(vARR1)!="undefined" && vARR1!="" && (vARR1.indexOf(",")!=-1 || vARR1.indexOf(";")!=-1)){ //check multiple coma or semi colon separated dx
								if(vARR1.indexOf(",")!=-1){ var ardx=vARR1.split(","); }else if(vARR1.indexOf(";")!=-1){ var ardx=vARR1.split(";"); }
								if(ardx.length>1){ //it will be greater than 1

									var ardxid=tmp_dx_id.split(",");

									for(var d=0;d<ardx.length;d++){
										var t = ""+$.trim(ardx[d]);
										var tid = (typeof(ardxid[d])!="undefined") ? ""+$.trim(ardxid[d]) : "" ;
										if(t!=""){
											if(d==0){  hiddenOBJ.val(t).data("dxid", tid); hiddenOBJ.triggerHandler("blur"); }
											else{  addAssessOptionExe("","", "", "", "", t,'','',1, tid); }//Add multiple dx  in multiple rows
										}
									}
								}
							}else{

								hiddenOBJ.val(vARR1).data("dxid", tmp_dx_id);
								hiddenOBJ.triggerHandler("blur");
							}
						}
					}else{
						//this.value = uV1;
						tmp_val = uV1;
						if(this.value.indexOf(";")==-1){
							hiddenOBJ.val('');
							if(flgInWv==1){show_asmt_site(hiddenOBJ, '');}
						}
					}

					//
					var t = jq_extractLast( this.value ,2, this);
					var r = ""+tmp_val;
					r=cn_adjustSearchValue(r,t[3]);
					if(this.strter!=""){r=$.trim(this.strter+" "+r);}
					//alert("\nc1:"+t[0]+"\nc2:"+r+"\nc3:"+t[1]);
					this.value =  $.trim(""+t[0]+""+r+t[1]);
					//

					$(this).triggerHandler("change");
					$(this).triggerHandler("blur");
					$(this).triggerHandler("click");
					//setCursorAtEnd(this,this.value);

					return false;
			}

			code = '';
			uV1 = assessment = ui.item.value;//UI selected value
			vARR1 	= uV1.split('[ICD-10: ');
			if(vARR1.length>1){
				ICD10_ARR1	= vARR1[1].split(',');
				if(ICD10_ARR1.length>1){
					uV1	= ICD10_ARR1[0];
					code = ICD10_ARR1[0];
					//assessment = vARR1[0];
				}
			}
			laterality = uV1.substr((uV1.length-1),1);
			if(showMode!='ap' && uV1.indexOf('.')>0 && uV1.indexOf('-')<0){
				ICD10		= uV1;
			}
			if(laterality=='-'){
				laterality = false;
				var terms = icd10_split( this.value );
				terms.pop();
				terms.push(ui.item.value);
				terms.push("");
				this.value = terms.join(">>");
				var tmp = typeof(ui.item.id)!="undefined" ? ui.item.id : "" ;
				if(tmp!=""){

					hiddenOBJ.data("dxid", tmp);
				}
			}else if(typeof(ICD10) != 'undefined' && ICD10!=null && typeof(ICD10) == 'string'){
				dX_code 	= ICD10.replace('-',laterality);
				if(showMode=='ap'){
					icd_value	= this.value;
					NewdX_code 	= ICD10.replace('-',laterality);
					dX_code		= icd_value.replace(ICD10,NewdX_code);
					dX_code		= dX_code.replace('>>','');

					vARR2 	= dX_code.split(' [ICD-10: ');
					if(vARR2.length>1){	assessment = vARR2[0];}

					if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){
						hiddenOBJ.val(NewdX_code);
					}
				}


				this.value 	= assessment;
				$(this).triggerHandler("change");
				$(this).triggerHandler("blur");
				$(this).triggerHandler("click");

			}else{
				if(showMode=='ap'){
					if(vARR1.length>1){
						this.value 	= vARR1[0];
						if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){
							hiddenOBJ.val(code);
							if(typeof(ui.item.id)!="undefined" && $.trim(ui.item.id)!=""){
								hiddenOBJ.data("dxid", ui.item.id);
							}

							//show site values
							if(flgInWv==1){show_asmt_site(hiddenOBJ, code);}
						}
						var fldid01 = ""+this.id;
						var fldid02=fldid01.replace("pop_","");

						if($("#dialogLSS").length>0){

							if($("#pop_ass_flag").length>0){
								if(fldid02.indexOf("elem_assessment_dxcode") != -1 || fldid02.indexOf("elem_assessment") != -1){
									var elem_ass_comm="";
									if($("#elem_ass_comm").val()!=""){
										elem_ass_comm = "; \n"+$("#elem_ass_comm").val();
									}
									$("#"+fldid02).val($("#"+fldid01).val()+elem_ass_comm);
									$("#dialogLSS").remove();
								}else{
									$("#dialogLSS").remove();
								}
							}
						}
						$("#"+fldid02).triggerHandler("click");
						$("#"+fldid02).triggerHandler("blur");
						$("#"+fldid02).triggerHandler("change");
					}else{
						//this.value 	= assessment;
						if(this.value.indexOf(";")==-1){
							if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){
								hiddenOBJ.val(code);
							}
						}

						//--
						//
						tmp_val = ""+ui.item.value;
						var t = jq_extractLast( this.value ,2, this);
						var r = ""+tmp_val;
						r=cn_adjustSearchValue(r,t[3]);
						if(this.strter!=""){r=$.trim(this.strter+" "+r);}

						//alert(""+r+"\n\n"+t);

						//alert("\nc1:"+t[0]+"\nc2:"+r+"\nc3:"+t[1]);
						this.value =  $.trim(""+t[0]+""+r+t[1]);
						//

						$(this).triggerHandler("change");
						$(this).triggerHandler("blur");
						$(this).triggerHandler("click");
						//setCursorAtEnd(this,this.value);
						//--

					}
				}else{

					this.value 	= assessment;

					$(this).triggerHandler("change");
					$(this).triggerHandler("blur");
					$(this).triggerHandler("click");
				}
			}

			return false;
		}
		});
		//end function--
		};

		$( "#assessplan .col-sm-7-as textarea[name^='elem_assessment']" ).bind("focus",function(){
			if($(this).attr('id')!=icd10_unique_obj_id){
		icd10_unique_obj_id = $(this).attr('id');
				delete uV;delete  vARR;delete   ICD10;delete  uV1;delete  vARR1;delete  ICD10_ARR1;delete  laterality;delete  dX_code;delete  NewdX_code;delete  ICD10_ARR;
			}
		});
		//$( "textarea[name^='elem_assessment']" ).each(function(index, element) {
           // bind_autocomp($("textarea[name^='elem_assessment']"),getDataFilePath,'ap');
       // });

		$( "#assessplan .col-sm-7-as textarea[name='elem_assessment[]'], #dialogLSS textarea[name='pop_elem_assessment[]'], #rprtIntrModal textarea[name='elem_assessment[]'] " ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta2(this);};});


	}else if(version==4){
		var ta3 = function(ota3){
			$( ota3 ).autocomplete({
			    //source: "common/requestHandler.php?elem_formAction=TypeAhead",
				source: "requestHandler.php?elem_formAction=TypeAhead"+strExamName,
			    minLength: 2,
			    autoFocus: true,
			    focus: function( event, ui ) {return false;},
			    select: function( event, ui ) { if(ui.item.value!=""){var t = jq_extractLast( this.value ,2, this);
					    var r = ""+ui.item.value;
					    r=cn_adjustSearchValue(r,t[3]);

					    if(this.strter!=""){r=$.trim(this.strter+" "+r);}
					    this.value =  $.trim(""+t[0]+""+r+t[1]);

					    $(this).triggerHandler("change");
					    $(this).triggerHandler("blur");
					    $(this).triggerHandler("click");

					    setCursorAtEnd(this,t[2]+r.length); } }
			}).filter("textarea[id^=elem_plan]").each(function(){$(this).triggerHandler('keyup');});
		};
		$( "textarea:not(textarea[name^='elem_assessment'])"+ptrntextbox  ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta3(this);};});

		//if exam then return
		if(typeof(examName)!="undefined" && examName!=""){return;}

		var ta4 = function(ota4){
			$(ota4).autocomplete({
			    source: "requestHandler.php?elem_formAction=TypeAhead&mode=assessment",
				minLength: 2,
			    autoFocus: true,
			    focus: function( event, ui ) {return false;},
			    select: function( event, ui ) {
				    if(ui.item.value!=""){
					    var t = jq_extractLast( this.value ,2, this);
					    var r = ""+ui.item.value;
					    r=cn_adjustSearchValue(r,t[3]);

					    //if(this.strter!=""){r=$.trim(this.strter+" "+r);}
					    this.value =  $.trim(""+t[0]+""+r);

					    $(this).triggerHandler("change");
					    $(this).triggerHandler("blur");
					    $(this).triggerHandler("click");

					   // setCursorAtEnd(this,t[2]+r.length);
					//$(this).triggerHandler("blur");
					//$( this ).autocomplete( "close" );
					return false;/*this.value=""+ui.item.value;$(this).trigger("keyup");*/} }
			});
		};
		$( "#assessplan textarea[name^='elem_assessment']"  ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta4(this);};});

	}

	if(typeof(examName)!="undefined" && examName!=""){return;}

	/*
	$("textarea:not(textarea[name^='elem_assessment'])").each(function(){
		new actb(this,arrTypeAhead);

		//resize plan+assess
		if(this.name.indexOf("elem_plan")!=-1){  //&& $(this).val() !=""
			$(this).trigger('keyup');
		}
	});
	*/

	//if(typeof(flg)!="undefined"&&flg=="ocumed"){

		var ta5 = function(ota5){
			$(ota5).autocomplete({
			    source: "requestHandler.php?elem_formAction=TypeAhead&mode=OcuMed",
			    minLength: 2,
			    autoFocus: true,
			    focus: function( event, ui ) {return false;},
			    select: function( event, ui ) { if(ui.item.value!=""){this.value=""+ui.item.value; if(this.id!="blank_med"){/*checkValidOcuMed(this);this.focus();*/}} }
			});
		};
		$( "#tblOcMeds input[type=text], #blank_med"  ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta5(this);};});

	//	return; //
	//}

	// ref to consult, doctor name
	if(typeof(multiphy_typeahead)!="undefined"){multiphy_typeahead(5);}

    // refferal code typeahead
	if(typeof(referral_code_typeahead)!="undefined"){referral_code_typeahead();}

	//Fu visit
	if(typeof(cn_ta_fu)!="undefined"){cn_ta_fu();}
	//
	//if(typeof(stopMouseWheelOnTextarea)!="undefined"){stopMouseWheelOnTextarea();}

	//color + Neuro/psych
	if(typeof(cn_ta_clr_npsych)!="undefined"){cn_ta_clr_npsych();}

	//procedures
	if(typeof(cn_ta_procedures)!="undefined"){cn_ta_procedures();}


}

function rv_lens_used(){
	if(typeof(examName)!="undefined" && examName=="Fundus"){
		if(typeof(arr_lens_used)!="undefined"){cn_ta_clr_npsych("#el_lens_used");}
		sb_dx_typeahead(); //dx codes
	}
}

function referral_code_typeahead() {
    var ta1 = function(ota1){
		$( ota1 ).autocomplete({
		    source: zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=referralCode",
		    minLength: 2,
		    autoFocus: true,
		    open: function () {   $(this).autocomplete('widget').zIndex(1000000); },
		    change: function( event, ui ) {    if(this.id.indexOf("elem_refer_code")!=-1){    ohid=this.id.replace(/(txt|elem)/,"hid"); if((!ui.item && $.trim(this.value)!="" && $.trim($("#"+ohid).val())=="")||$.trim(this.value).length<2){ this.value="";  $("#"+ohid).val(""); }  }},
		    response: function( event, ui ) {  if(this.id.indexOf("elem_refer_code")!=-1){  ohid=this.id.replace(/(txt|elem)/,"hid");	 $("#"+ohid).val("");  	} },
		    select: function( event, ui ) {
					ohid=this.id.replace(/(txt|elem)/,"hid");
					if(ui.item.value!=""){
						this.value=""+ui.item.value;
						$("#"+ohid).val(this.value);
						$(this).triggerHandler("mouseover");
					}
				}
		});
	};

	$( ":input[type=text][id='elem_refer_code']" ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta1(this);}; });
}

function  cn_ta_procedures(){

	//meds
	var ta5 = function(ota5){
	$( ota5 ).autocomplete({
		    source: zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=getorder",
		    minLength: 2,
		    autoFocus: true,
		    focus: function( event, ui ) {return false;},
		    select: function( event, ui ) {if(ui.item.value!=""){this.value=""+ui.item.value; this.onchange(); set_qty_in_hand(this,arr_med_name,arr_item_no_qty,arr_thrash,arr_lot_no); }}
		});
	};
	$( "#divPreOpMeds input[type=text], #divIntraVitrealMeds  input[type=text], #divPostOpMeds  input[type=text]"  ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta5(this);};});

	//
	//Providers
	var ta6 = function(ota6){
	$(":input[name=elem_providers]").autocomplete({
	   // source: "procedures.php?elem_formAction=provider",
		source: function( request, response ) {
		    $.getJSON( zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=getproviders", {
			term: jq_extractLast( request.term ,1)
		    }, response );
		},
		search: function() {
		    // custom minLength
		    var term = jq_extractLast( this.value ,1);
		    var o = jq_getStarter(term);
		    this.strter=o[0]; //

		    if ( o[1].length < 2 ) {
			return false;
		    }
		},

	    minLength: 2,
	    autoFocus: true,
	    focus: function( event, ui ) {return false;},
	    select: function( event, ui ) { if(ui.item.value!=""){
			var t = jq_extractLast( this.value ,2);
			    var r = ""+ui.item.value;
			    if(this.strter!=""){r=$.trim(this.strter+" "+r);}
			    this.value =  $.trim(""+t+""+r+",");

			    $(this).triggerHandler("change");
			    $(this).triggerHandler("blur");
			    $(this).triggerHandler("click");

			return false;
		} }
	});
	};
	$( ":input[name=elem_providers]"  ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta6(this);};});

	cn_ta_clr_npsych("#elem_iopType");
}

function cn_ta_clr_npsych(str){
	var ta5 = function(ota5){
		$(ota5).autocomplete({
		    source: function( request, response ) {
				var oe = this.element[0];
			  var tags = [];
				var flgSrch=0;
				if(oe && oe.name && oe.name.indexOf("eyeColorCc")!=-1){
					tags = ["Amber", "Black", "Blue", "Brown", "Grey", "Green", "Hazel", "Violet"];
				}else if(oe && oe.name && oe.name.indexOf("elem_mfocal")!=-1){
					tags = ["Restor", "Tecnis"];
				}else if(oe && oe.name && oe.name.indexOf("elem_descTa")!=-1){
					tags = ["Squeezing", "Holding lids", "Taped lids", "Unreliable", "Unable" ];
					flgSrch=1;
				}else if(oe && oe.name && $(oe).hasClass("iop_method")){
					tags = ["Applanation", "Pnuematic", "Puff", "Tactile", "Tonopen" ];
				}else if(oe && oe.name && oe.name.indexOf("el_lens_used")!=-1){
					tags = arr_lens_used;
				}else{
					tags =["Agitated", "AAOx3", "Confused", "Flat", "Cognitive Impairment", "Too Young (Pediatric Patient)", "Uncooperative"];
				}
				var matcher = new RegExp( "" + $.ui.autocomplete.escapeRegex( request.term ), "i" );
				  response( $.grep( tags, function( item ){
				      return flgSrch || matcher.test( item );
				  }) );
			      },
		    minLength: 0,
		    autoFocus: true,
		    focus: function( event, ui ) {return false;},
		    select: function( event, ui ) {
					if(ui.item.value!=""){
						var t = ""+ui.item.value;
						if( this.name && this.name.indexOf("elem_descTa")!=-1){
							var s = this.value, tid=this.id, t1;
							if(typeof(s)!="undefined" && s!=""){
								t1 = s +" "+ t;
								$("#"+tid).val(t1);
								return false;
							}
						}else{
							this.value = ""+t1;
						}
					}
			}
		});
	};
	if(typeof(str)=="undefined" || $.trim(str)==""){
		str = "#elem_eyeColorCc, #elem_mfocalOd_pciol_opts, #elem_mfocalOs_pciol_opts";
	}
	$( ""+str  ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta5(this);}; $( this ).autocomplete( "search", "" ); });
}

function multiphy_typeahead(id){
	if(typeof(zPath)!="undefined"&&zPath!=""){}else{zPath="";}

	var obj;
	if(id == "2"){
		obj="txtCoPhyArr";
	}else if(id == "3"){
		obj="txtPCPMedHxArr";
	}else if(id == "4"){
		obj="txtPCPDemoArr";
	}else if(id == "5"){
		obj="elem_doctorName_refphy";
	}else if(id == "6"){
		obj="elem_fsta_phy_name";
	}else{
		obj="txtRefPhyArr";
	}

	var u = (typeof(zPath_remote) != "undefined") ? zPath_remote : zPath;

	var ta6 = function(ota6){
		$( ota6 ).autocomplete({
		    source: u+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=refPhy&w="+id,
		    minLength: 2,
		    autoFocus: true,
		    open: function () {   $(this).autocomplete('widget').zIndex(1000000); },
		    focus: function( event, ui ) { ohid=this.id.replace(/(txt|elem)/,"hid"); if(typeof(reff_multi_add_popup)!="undefined"){ reff_multi_add_popup($(this),document.getElementById(ohid),'WV');} return false;},
		    change: function( event, ui ) {    if(this.id.indexOf("elem_doctorName_refphy")!=-1){    ohid=this.id.replace(/(txt|elem)/,"hid"); if((!ui.item && $.trim(this.value)!="" && $.trim($("#"+ohid).val())=="")||$.trim(this.value).length<2){ this.value="";  $("#"+ohid).val(""); }  }},
		    response: function( event, ui ) {  if(this.id.indexOf("elem_doctorName_refphy")!=-1){  ohid=this.id.replace(/(txt|elem)/,"hid");	 $("#"+ohid).val("");  	} },
		    select: function( event, ui ) {
					ohid=this.id.replace(/(txt|elem)/,"hid");
					if(ui.item.value!=""){
						this.value=""+ui.item.value;
						$("#"+ohid).val(""+ui.item.refid);
						reff_multi_add_popup($(this),document.getElementById(ohid),'WV');
						if(obj=="elem_doctorName_refphy"){ $(this).triggerHandler("mouseover"); }
						if(id == "6" && ui.item.address){ $(":input[name=elem_fsta_phy_address]").val(""+ui.item.address);  }
					}
				}
		});
	};

	$( ":input[type=text][id*='"+obj+"']" ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta6(this);}; });

}

//reff_multi_add_popup is defined in main/javascript/common.js
//
function getIcdContructed(ICD10,liter,sever,stage){
	var ICD10_tmp = ICD10;
	if(liter!=""&&typeof(liter)!="undefined"){
		/*
		if(ICD10_tmp.substr((ICD10_tmp.length-1),1)=='-'){//
			var tt =ICD10_tmp.substr(0,(ICD10_tmp.length-1));
			ICD10_tmp=tt+liter;
		}
		*/

		//

		var lit_w=6;

		// DM codes --
		if((ICD10_tmp.indexOf("-")!=-1 && (ICD10_tmp.indexOf("E1")!=-1||ICD10_tmp.indexOf("E0")!=-1) && ICD10_tmp.indexOf(".9")==-1)){
			lit_w=7;
		}
		//--

		if(ICD10_tmp.charAt(lit_w)=='-'){//
			var tt =ICD10_tmp.substr(0,lit_w);
			var tt1 =ICD10_tmp.substr(lit_w+1);
			ICD10_tmp=tt+liter+tt1;
		}else{
			//if(ICD10_tmp.substr(-3,3)!='-X-' || ICD10_tmp.substr(-3,3)!='-x-'){
				var tt =ICD10_tmp.substr(0,5);
				var tt1 =ICD10_tmp.substr(6);
				ICD10_tmp=tt+liter+tt1;
			//}
		}
	}

	//alert(ICD10_tmp);

	if(sever!=""&&typeof(sever)!="undefined"){
		ICD10_tmp 	= ICD10_tmp.replace('-',sever);
	}else if(stage!=""&&typeof(stage)!="undefined"){
		ICD10_tmp 	= ICD10_tmp.replace('-',stage);
	}
	return ICD10_tmp;
}
function icd10_split(val){ var x = new RegExp(">>\s*");  return val.split( x ); }
function icd10_extractLast(term){return icd10_split( term ).pop();}
function cn_resetasval(id,val,wh,otd){

	//console.log(id+","+val+","+wh+","+otd);
	//return;

	var sever_full=[], liter_full=[], stage_full=[];
	var sever=[], liter=[], stage=[];
	var t_sever=[], t_sever_full=[],t_stage=[],t_stage_full=[];
	var f_svr_stg_lid=0;
	var t_opt=0;
	if($("#dialogLSS").length>0){

	if(($("#dialogLSS table[id*=LSS_od0]").length>0 || $("#dialogLSS table[id*=LSS0]").length>0) && $("#dialogLSS table[id*=LSS1]").length<=0 && $("#dialogLSS table[id*=LSS2]").length<=0){
		t_opt=parseInt(t_opt)+1;
	}
	/*if($("#dialogLSS table[id*=LSS0]").length>0){
		t_opt=parseInt(t_opt)+1;
	}
	if($("#dialogLSS table[id*=LSS1]").length>0){
		t_opt=parseInt(t_opt)+1;
	}
	if($("#dialogLSS table[id*=LSS2]").length>0){
		t_opt=parseInt(t_opt)+1;
	}*/
	var len_multi = $("#dialogLSS #olen").val();
	if(t_opt==1){
		if($(otd).attr("class")=='highlight' || $(otd).attr("class")=='highlight_2' || $(otd).attr("class")=='highlight highlight_2'){
			$(otd).removeClass("highlight highlight_2");
		}else{
			$(otd).addClass("highlight");
		}
	}else{
		if($("#dialogLSS table[id*=LSS_od0]").length>0 || $("#dialogLSS table[id*=LSS0]").length>0 || $("#dialogLSS table[id*=LSS1]").length>0 || $("#dialogLSS table[id*=LSS2]").length>0){
			var otd_id=$(otd).attr('id');
			var otd_id_exp=otd_id.split('_');
			if(otd_id_exp[1]=='lat'){

				if($(otd).attr("class")=='highlight' || $(otd).attr("class")=='highlight_2' || $(otd).attr("class")=='highlight highlight_2'){
					$(otd).removeClass("highlight highlight_2");
				}else{
					if($("#dialogLSS table[id*=LSS]  td.doneLPU").length>0 || $("#dialogLSS table[id*=LSk3] td.doneCF").length>0){
					}else{
						if($("#dialogLSS td[id*=txt_lat_1]").hasClass("highlight")==true && val==1){
							$(otd).removeClass("highlight highlight_2");
						}else if($("#dialogLSS td[id*=txt_lat_2]").hasClass("highlight")==true && val==2){
							$(otd).removeClass("highlight highlight_2");
						}else if($("#dialogLSS td[id*=txt_lat_2]").hasClass("highlight")==false && val==2){
							$(otd).parent().parent().find("td.highlight").removeClass("highlight highlight_2");
							$(otd).addClass("highlight");
						}else if($("#dialogLSS td[id*=txt_lat_1]").hasClass("highlight")==false && val==1){
							$(otd).parent().parent().find("td.highlight").removeClass("highlight highlight_2");
							$(otd).addClass("highlight");
						}else{
							$(otd).addClass("highlight");
						}
					}
				}

				if($("#dialogLSS table[id*=LSS]  td.doneLPU").length>0 || $("#dialogLSS table[id*=LSk3] td.doneCF").length>0){
					if($("#dialogLSS div.dvLss table[id=bigtbl1] td[id*=txt_lat_]").hasClass("highlight")==false){
						$("#dialogLSS div.dvLss table#bigtbl1").remove();
						$("#dialogLSS #olen").val(parseInt($("#dialogLSS #olen").val())-1);
					}
					if($("#dialogLSS #olen").val()==0){
						$("#dialogLSS #olen").val(1);
					}
					len_multi = $("#dialogLSS #olen").val();
				}else{
					var tot_lat=$("#dialogLSS div.dvLss table[id*=bigtbl]").find("table[id*=LSS0]").length;
					var tot_h_lat=$("#dialogLSS div.dvLss table[id*=bigtbl]").find("table[id*=LSS0] td.highlight").length;
					if($(otd).parents("table[id*=bigtbl]").find("table[id*=LSS0] td.highlight").length>0){
						if($("#dialogLSS td[id*=txt_lat_3]").hasClass("highlight")==true){
							if(val==3){
								$("#dialogLSS div.dvLss table#bigtbl1").remove();
								$("#dialogLSS table[id*=bigtbl] .highlight").removeClass("highlight highlight_2");
								$("#dialogLSS td[id*=txt_lat_3]").addClass("highlight");
							}else if(val<3){
								$("#dialogLSS td[id*=txt_lat_3]").removeClass("highlight highlight_2");
								$("#dialogLSS").append("");
								$("#dialogLSS div.dvLss table#bigtbl").clone().appendTo("#dialogLSS  div.dvLss");
								var len = $("#dialogLSS table[id*=bigtbl]").length;
								var inx = len - 1;
								var oo = $("#dialogLSS table[id*=bigtbl]").get(inx);
								oo.id = "bigtbl"+inx;
								$("#dialogLSS table[id*="+oo.id+"] td").removeClass("highlight highlight_2");
								$("#dialogLSS #olen").val(len);
								$($("#dialogLSS table[id*="+oo.id+"] td[id="+otd_id+"]")).data("vval",val);
							}
						}else if(tot_lat>1){
							//$(otd).removeClass("highlight highlight_2");
						}else{
							//$(otd).removeClass("highlight highlight_2");
							$("#dialogLSS").append("");
							$("#dialogLSS div.dvLss table#bigtbl").clone().appendTo("#dialogLSS  div.dvLss");
							var len = $("#dialogLSS table[id*=bigtbl]").length;
							var inx = len - 1;
							var oo = $("#dialogLSS table[id*=bigtbl]").get(inx);
							oo.id = "bigtbl"+inx;
							$("#dialogLSS table[id*="+oo.id+"] td").removeClass("highlight highlight_2");
							$("#dialogLSS #olen").val(len);
							//alert($($("#dialogLSS table[id*="+oo.id+"] td[id="+otd_id+"]")));
							//$($("#dialogLSS table[id*="+oo.id+"] td[id="+otd_id+"]")).addClass("highlight");
							$($("#dialogLSS table[id*="+oo.id+"] td[id="+otd_id+"]")).data("vval",val);
						}
					}
				}
			}else{
				$(otd).parents("table[id*=LSS]").find("td").removeClass("highlight highlight_2");
				$(otd).addClass("highlight");
			}
		}
	}
	$(otd).data("vval",val);


	//console.log(id+","+val+","+wh+","+otd);

	//
	//
	/*if($("#dialogLSS table[id*=LSS]").length > $("#dialogLSS table[id*=LSS] td.highlight").length||
		($("#dialogLSS table[id*=LSk3]").length>0 && $("#dialogLSS table[id*=LSk3] td.doneCF").length<=0 && $("#dialogLSS table[id*=LSk3] label.highlight").length==0)
		){
		if(($("#dialogLSS table[id*=LSS_o]").length>0 && $("#dialogLSS table[id*=LSS_o]  td.doneLPU").length>0 )){//
		}else{
			return;
		}
	}*/

	if($("#dialogLSS table[id*=LSS]  td.doneLPU").length>0 || $("#dialogLSS table[id*=LSk3] td.doneCF").length>0){
		$("#dialogLSS table[id*=LSS] td").removeClass("doneLPU");
	}else{
		return;
	}
	//alert(len_multi);
	//
	for(var k=0;k<len_multi;k++){

	//var btid = $(otd).parents("table[id*=bigtbl]").attr("id");
	var btid_indx = k;//parseInt(btid.replace("bigtbl",""));
	var btid_spx=k;
	if(btid_indx==0){ btid_indx=0; btid_spx=""; }

	//alert("#dialogLSS #bigtbl"+btid_spx);

	if($("#dialogLSS #bigtbl"+btid_spx).length<=0){ continue; }
	if(t_opt==1){
		//lids
		var btid_indx_tmp = 0;
		$('.highlight').each(function(){
			var txt_lat_id= $(this).attr('id');
			var txt_lat_id_exp= txt_lat_id.split('_');
			//if(txt_lat_id_exp[1]=='lat'){
				btid_indx_tmp = parseInt(btid_indx_tmp)+1;
				//alert(btid_indx_tmp+'-'+$("#"+txt_lat_id).data("vval"));
				if(txt_lat_id_exp[1]=='lat'){
					liter[btid_indx_tmp] = $("#"+txt_lat_id).data("vval");
					liter_full[btid_indx_tmp] = $("#"+txt_lat_id).html();
				}
				if(txt_lat_id_exp[1]=='sev'){
					sever[btid_indx_tmp] = $("#"+txt_lat_id).data("vval");
					sever_full[btid_indx_tmp] = $("#"+txt_lat_id).html();
				}
				if(txt_lat_id_exp[1]=='stag'){
					stage[btid_indx_tmp] = $("#"+txt_lat_id).data("vval");
					stage_full[btid_indx_tmp] = $("#"+txt_lat_id).html();
				}

			//}
		});
		len_multi=btid_indx_tmp;
		/*
		var btid_indx_tmp = -1;

		if($("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS_od0]").length>0 && $("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS_od0] td.highlight").length >0 ){

			var btid_indx_tmp = parseInt(btid_indx)+parseInt(btid_indx);
			//alert(btid_indx_tmp);
			liter[btid_indx_tmp] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS_od0] td.highlight").data("vval");
			liter_full[btid_indx_tmp] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS_od0] td.highlight").html();

			//
			if(sever.length>0){
				t_sever[btid_indx_tmp] = 	sever[btid_indx];
				t_sever_full[btid_indx_tmp] = sever_full[btid_indx];
			}else if(stage.length>0){
				t_stage[btid_indx_tmp] = stage[btid_indx];
				t_stage_full[btid_indx_tmp] = stage_full[btid_indx];
			}

		}
		if($("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS_os0]").length>0 && $("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS_os0] td.highlight").length >0 ){
			var btid_indx_tmp = (btid_indx_tmp == -1) ? parseInt(btid_indx)+parseInt(btid_indx) : parseInt(btid_indx)+parseInt(btid_indx)+1;
			//alert(btid_indx_tmp);
			liter[btid_indx_tmp] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS_os0] td.highlight").data("vval");
			liter_full[btid_indx_tmp] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS_os0] td.highlight").html();
			len_multi=btid_indx_tmp+1; // if lids len is 1 + len

			//
			if(sever.length>0){
				t_sever[btid_indx_tmp] = 	sever[btid_indx];
				t_sever_full[btid_indx_tmp] = sever_full[btid_indx];
			}else if(stage.length>0){
				t_stage[btid_indx_tmp] = stage[btid_indx];
				t_stage_full[btid_indx_tmp] = stage_full[btid_indx];
			}

		}
		*/
	}else{
			if($("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS1]").length>0 && $("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS1] td.highlight").length >0 ){
				sever[btid_indx] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS1] td.highlight").data("vval");
				sever_full[btid_indx] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS1] td.highlight").html();
			}

			if($("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS2]").length>0 && $("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS2] td.highlight").length >0 ){
				stage[btid_indx] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS2] td.highlight").data("vval");
				stage_full[btid_indx] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS2] td.highlight").html();
			}

			if($("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS0]").length>0 && $("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS0] td.highlight").length >0 ){
				liter[btid_indx] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS0] td.highlight").data("vval");
				liter_full[btid_indx] = ""+$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS0] td.highlight").html();
			}
		}
	}

	//Check if lids, stage/sever exists in pop up--
	if(f_svr_stg_lid==0 && $("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS0]").length>0 &&
		($("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS1]").length>0||$("#dialogLSS #bigtbl"+btid_spx+" table[id*=LSS2]").length>0) ){ f_svr_stg_lid=1; }

	//check t_sever or t_stage: when lid with severity or stage
	if(t_sever.length>sever.length){
		sever = t_sever;
		sever_full = t_sever_full;
	}else if(t_stage.length>stage.length){
		stage = t_stage;
		stage_full=t_stage_full;
	}

	//$("#dialogLSS").dialog( "destroy" );$("#dialogLSS").remove();

	}//

	//alert(len_multi);

	//
	//alert(id +" - "+ liter[1]+" - "+sever[1] +" -- "+ liter[0]+" - "+sever[0]);

	var assess = $("#"+id).val();
	var oassess = $("#"+id).get(0);
	var valChkdb= $.trim($("#"+id).data("valbakdb"));
	//alert(oassess.id);
	//alert(valChkdb);
	//return;

	//--
	var arr = assess.split("~~"); //ui.item.value
	index = $("#"+id).attr('id').match(/\d+$/);
	if(id.indexOf("elem_assessment_dxcode") == -1 && !$("#"+id).hasClass("dxallcodes")){
		hiddenOBJ = $("#elem_assessment_dxcode"+index);
		if(oassess.value.indexOf(";")==-1){
			if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){hiddenOBJ.val('');}
		}
	}

	//alert("dd");

	var code = '';
	var uV1 = assessment = assess;//UI selected value
	var vARR1 	= uV1.split('[ICD-10: ');
	if(vARR1.length>1){
		ICD10_ARR1	= vARR1[1].split(',');
		if(ICD10_ARR1.length>1){
			uV1	= ICD10_ARR1[0];
			code = ICD10_ARR1[0];
			//assessment = vARR1[0];
		}
	}

	//alert("33");

	//laterality = ""+eye;//uV1.substr((uV1.length-1),1);
	/*
	if(laterality=='-'){
		alert("ff");
		/*
		laterality = false;
		var terms = icd10_split( this.value );
		terms.pop();
		terms.push(ui.item.value);
		terms.push("");
		this.value = terms.join(">>");
		*/
		/*
		$(this).triggerHandler("change");
		$(this).triggerHandler("blur");
		$(this).triggerHandler("click");
		*-/

	}else
	*/

	//alert("ff"+valChkdb);

	//
	if(typeof(valChkdb)=="string" && valChkdb!=""){
		//alert(valChkdb);
		ICD10=""+valChkdb;
	}

	if(typeof(ICD10) == 'string' && ICD10!=null){

		//alert("2ii: "+ICD10); //ICD10	= ICD10_ARR[0];

		var ICD10_tmp = getIcdContructed(ICD10,liter[0],sever[0],stage[0]);
		dX_code 	= ICD10_tmp;

		var dx_code_modifier_text="";
		var dcmtmp="";
		/*if(typeof(liter_full[0])!="undefined"&&liter_full[0]!="") { dcmtmp+=liter_full[0]+" "; }
		if(typeof(sever_full[0])!="undefined"&&sever_full[0]!="") { dcmtmp+=sever_full[0]+" "; }
		if(typeof(stage_full[0])!="undefined"&&stage_full[0]!="") { dcmtmp+=stage_full[0]+" "; }
		dx_code_modifier_text+=dcmtmp;*/


		if((typeof(showMode)!="undefined" && showMode=='ap')||
			id.indexOf("elem_assessment_dxcode") != -1 || $("#"+id).hasClass("dxallcodes") ){
			icd_value	= oassess.value;
			NewdX_code 	= dX_code;//ICD10.replace('-',laterality);
			dX_code		= icd_value.replace(ICD10,NewdX_code);
			dX_code		= dX_code.replace('>>','');

			vARR2 	= dX_code.split(' [ICD-10: ');
			if(vARR2.length>1){
				assessment = vARR2[0];
			}

			//alert(len_multi);
			//add multi --
			if(len_multi>0){
				var str_as_m="";
				if(t_opt==1){
					for(var t_m=1;t_m<=len_multi;t_m++){
						//alert(ICD10+" - "+liter[t_m]+" - "+sever[t_m]+" - "+stage[t_m]);
						var ICD10_tmp_m = getIcdContructed(ICD10,liter[t_m],sever[t_m],stage[t_m]);
						if(ICD10_tmp_m!=""){
							if(str_as_m!=""){str_as_m+=","; }
							str_as_m 	+= ICD10_tmp_m;
							if(dx_code_modifier_text!=""){ dx_code_modifier_text=$.trim(dx_code_modifier_text)+", "; }

							var dcmtmp="";
							if(typeof(liter_full[t_m])!="undefined"&&liter_full[t_m]!="") { dcmtmp+=liter_full[t_m]+" "; }
							if(typeof(sever_full[t_m])!="undefined"&&sever_full[t_m]!="") { dcmtmp+=sever_full[t_m]+" "; }
							if(typeof(stage_full[t_m])!="undefined"&&stage_full[t_m]!="") { dcmtmp+=stage_full[t_m]+" "; }
							dx_code_modifier_text+=dcmtmp;

						}
						//*/
					}
				}else{
					for(var t_m=0;t_m<len_multi;t_m++){
						//alert(ICD10+" - "+liter[t_m]+" - "+sever[t_m]+" - "+stage[t_m]);
						var ICD10_tmp_m = getIcdContructed(ICD10,liter[t_m],sever[t_m],stage[t_m]);
						if(ICD10_tmp_m!=""){
							if(str_as_m!=""){str_as_m+=","; }
							str_as_m 	+= ICD10_tmp_m;
							if(dx_code_modifier_text!=""){ dx_code_modifier_text=$.trim(dx_code_modifier_text)+", "; }

							var dcmtmp="";
							if(typeof(liter_full[t_m])!="undefined"&&liter_full[t_m]!="") { dcmtmp+=liter_full[t_m]+" "; }
							if(typeof(sever_full[t_m])!="undefined"&&sever_full[t_m]!="") { dcmtmp+=sever_full[t_m]+" "; }
							if(typeof(stage_full[t_m])!="undefined"&&stage_full[t_m]!="") { dcmtmp+=stage_full[t_m]+" "; }
							dx_code_modifier_text+=dcmtmp;

						}
						//*/
					}
				}

				if(str_as_m!=""){

					//get all dx codes: and replace dx code with is selected,
					if(id.indexOf("elem_assessment_dxcode") != -1&&icd_value.indexOf(",")!=-1&&icd_value.indexOf(">>")==-1){
						//check if dxcode belong to same family--
						var check_dx_family_val = f_svr_stg_lid; //is_dx_same_family(icd_value);
						if(check_dx_family_val=="0"){
							var tmp = icd_value.replace(ICD10,"");
							tmp = $.trim(tmp);
							tmp = tmp.replace(/,$/,"");

							var ar_t = tmp.split(",");
							var ar_as_m = str_as_m.split(",");

							if(ar_t.length<ar_as_m.length){ //append only if values are added
								str_as_m = str_as_m+","+tmp;
							}

							// unique dx code --
							if(ar_as_m.length>0){
								ar_as_m = jQuery.unique(ar_as_m);
								str_as_m = ar_as_m.join(", ");
							}
							//--
						}
					}

					NewdX_code = str_as_m;
				}

			}
			//add multi --
			if(id.indexOf("elem_assessment_dxcode") != -1 || $("#"+id).hasClass("dxallcodes") ){
				assessment = NewdX_code;

			}else{

				//assessment
				if(id.indexOf("elem_assessment") != -1){
					assessment+="; "+dx_code_modifier_text;
				}

				if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){
					hiddenOBJ.val(NewdX_code);
					hiddenOBJ.attr("title",NewdX_code);

					hiddenOBJ.trigger("change");
					hiddenOBJ.trigger("blur");

				}
			}
		}

		oassess.value  = assessment;

		//multiple in superbill
		if(oassess.id.indexOf("elem_dxCode")!=-1 && oassess.value!="" && oassess.value.indexOf(",")!=-1){
			var t_ar_su_dx=oassess.value.split(",");
			var t_su_dxid=$(oassess).data("dxid");
			if(typeof t_su_dxid == "undefined"){ t_su_dxid=""; }
			t_su_dxid=$.trim(t_su_dxid);
			oassess.value  =""; $(oassess).data("dxid","");
			for(var tinx in t_ar_su_dx){
				var tdx = $.trim(t_ar_su_dx[tinx]);
				if(tdx!="" && $("input[id*=elem_dxCode][value='']").length>0&&$("input[id*=elem_dxCode][value='"+tdx+"']").length<=0){
					var flgbrk=0;
					$("input[id*=elem_dxCode][value='']").each(function(){  if($.trim(this.value)==""&&flgbrk==0){     this.value=tdx; $(this).data("dxid",t_su_dxid);   $(this).trigger("blur");   flgbrk=1;   }  });
				}
			}
		}


		//if first Eye option is not empty and elem is assessment or assessment_dx_code
		if(id.indexOf("elem_assessment") != -1 && liter && typeof(liter)!="undefined"){
			//assess indx
			var asindx = id.replace(/(elem_assessment_dxcode|elem_assessment)/gi,"");
			if($.inArray('3',liter)!= -1 && $.inArray("Both Eyes",liter_full)!= -1){
				if($("#elem_apOu"+asindx).is(':checked')==false){
					$("#elem_apOs"+asindx+", #elem_apOd"+asindx).prop("checked", false);
					$("#elem_apOu"+asindx).trigger("click");
				}
			}else if($.inArray('2',liter)!= -1 && $.inArray('1',liter)!= -1 && $.inArray("Left Eye",liter_full)!= -1 && $.inArray("Right Eye",liter_full)!= -1){
				if($("#elem_apOu"+asindx).is(':checked')==false){
					$("#elem_apOs"+asindx+", #elem_apOd"+asindx).prop("checked", false);
					$("#elem_apOu"+asindx).trigger("click");
				}
			}else if ($.inArray('2',liter)!= -1 && $.inArray("Left Eye",liter_full)!= -1){
				if($("#elem_apOs"+asindx).is(':checked')==false){
					$("#elem_apOu"+asindx+", #elem_apOd"+asindx).prop("checked", false);
					$("#elem_apOs"+asindx).trigger("click");
				}
			}else if ($.inArray('1',liter)!= -1 && $.inArray("Right Eye",liter_full)!= -1 ){
				if($("#elem_apOd"+asindx).is(':checked')==false){
					$("#elem_apOs"+asindx+", #elem_apOu"+asindx).prop("checked", false);
					$("#elem_apOd"+asindx).trigger("click");
				}
			}else {
				var teye1="", teye2="";
				if(($.inArray('1',liter)!= -1 && $.inArray("Right Upper Lid",liter_full)!= -1)||($.inArray('2',liter)!= -1 && $.inArray("Right Lower Lid",liter_full)!= -1)){teye2="1";}
				if(($.inArray('4',liter)!= -1 && $.inArray("Left Upper Lid",liter_full)!= -1)||($.inArray('5',liter)!= -1 && $.inArray("Left Lower Lid",liter_full)!= -1)){teye1="1";}
				if(teye1=="1"&&teye2=="1"){
					if($("#elem_apOu"+asindx).is(':checked')==false){
						$("#elem_apOu"+asindx).trigger("click");
					}
				}else if(teye1=="1"){
					if($("#elem_apOs"+asindx).is(':checked')==false){
						$("#elem_apOs"+asindx).trigger("click");
					}
				}else if(teye2=="1"){
					if($("#elem_apOd"+asindx).is(':checked')==false){
						$("#elem_apOd"+asindx).trigger("click");
					}
				}
			}
		}

		//set title
		var asid = id.replace("_dxcode","");
		if(id.indexOf("elem_assessment_dxcode") != -1){
			oassess.title=assessment;

			//assess id
			//var asid = id.replace("_dxcode","");
			var tv_as = $("#"+asid).val();
			var tv_asp = $("#"+id).val();
			var tv_asp_val="";
			if(tv_asp.indexOf("]>>") != -1){
				if(tv_asp.indexOf(",")!=-1){
					var tv_asp_exp = tv_asp.split(",");
					if(tv_asp_exp.length>1){
						for(var ik=0;ik<tv_asp_exp.length;ik++){
							if(tv_asp_exp[ik].indexOf("[")==-1 && tv_asp_exp[ik].indexOf("]")==-1 && tv_asp_exp[ik].match(/[0-9]/)!==null){
								if(tv_asp_val!=""){
									tv_asp_val+= ',';
								}
								tv_asp_val+= tv_asp_exp[ik];
							}
						}
						$("#"+id).val(tv_asp_val);
					}
				}
			}
			if(typeof(tv_as)!="undefined"&&tv_as!=""){
				if(tv_as.indexOf(";")==-1){
				tv_as+=";";
				}else{
					var tmp_tv_as = tv_as.split(";");
					tv_as = tmp_tv_as[0]+";";
				}
			}
			var tv_as_exp= tv_as.split('[ICD-10: ');
			if(tv_as_exp.length>1){
				tv_as = $.trim(tv_as_exp[0])+";";
			}else{
				if($("#pop_"+asid)){
					tv_as = $.trim($("#pop_"+asid).val())+";";
				}
			}
			tv_as+=" "+dx_code_modifier_text;
			if($("#elem_ass_comm").val()!=""){
				tv_as+=";\n"+$("#elem_ass_comm").val();
			}
			$("#"+asid).val(tv_as).triggerHandler("change");
			$("#"+asid).triggerHandler("blur");
			$("#"+asid).triggerHandler("keyup");

		}else{
			if(id.indexOf("elem_assessment") != -1){
				if($("#elem_ass_comm").val()!=""){
					oassess.value=assessment+";\n"+$("#elem_ass_comm").val();
				}

			}
		}

		if($("#LSk3")){
			var extra_dx_code_id="";
			$("input[name='elem_sub_dx']").each(function(){
				if($('#'+this.id).is(':checked')){
					extra_dx_code_id=this.id.replace("elem_sub_","");
				}
			});
			if(extra_dx_code_id>0){
				cn_set_sub_dx(extra_dx_code_id,'yes');
			}
		}

		$(oassess).triggerHandler("change");
		$(oassess).triggerHandler("blur");
		$(oassess).triggerHandler("keyup");
		//$(oassess).triggerHandler("click");

		//check if '-' still exists then show pop up again : ticket
		if(id.indexOf("elem_assessment_dxcode") != -1&&icd_value.indexOf(",")!=-1){
			if($(oassess).val().indexOf("-")!=-1&&f_svr_stg_lid==0){ $("#"+asid).triggerHandler("click");   }
		}

	}else{

		//alert("33");

		if(typeof(showMode)!="undefined" && showMode=='ap'){
			if(vARR1.length>1){
				oassess.value 	= vARR1[0];
				$(oassess).triggerHandler("change");
				$(oassess).triggerHandler("blur");
				//$(oassess).triggerHandler("click");
			}else{
				//this.value 	= assessment;
				if(oassess.value.indexOf(";")==-1){
					if(typeof(hiddenOBJ)!='undefined' && hiddenOBJ.get(0)){
						hiddenOBJ.val(code);
					}
				}

				//--
				//
				tmp_val = ""+ui.item.value;
				var t = jq_extractLast( oassess.value ,2, oassess);
				var r = ""+tmp_val;
				r=cn_adjustSearchValue(r,t[3]);
				if(oassess.strter!=""){r=$.trim(oassess.strter+" "+r);}

				//alert(""+r+"\n\n"+t);

				//alert("\nc1:"+t[0]+"\nc2:"+r+"\nc3:"+t[1]);
				oassess.value =  $.trim(""+t[0]+""+r+t[1]);
				//

				$(oassess).triggerHandler("change");
				$(oassess).triggerHandler("blur");
				$(oassess).triggerHandler("click");
				//setCursorAtEnd(this,this.value);
				//--

			}
		}else{

			oassess.value 	= assessment;

			$(oassess).triggerHandler("change");
			$(oassess).triggerHandler("blur");
			$(oassess).triggerHandler("click");
		}
	}


	//--

}

function epost_addTypeAhead(wh, zpth){

	var wh = (typeof(wh) != "undefined" && wh != "") ? wh : ".epost textarea";
	var zPath1 = (typeof(zpth) != "undefined" && zpth!="") ? zpth : zPath;
	var ta8 = function(ota8){
		$( ota8 ).autocomplete({
		    //source: zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead",
		    source: function( request, response ) {
				$.getJSON( zPath1+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead", {
					term: jq_extractLast( request.term ,1,this.element[0])
				    }, response );
			    },
			search: function() {
			   var term = jq_extractLast( this.value ,1, this);
			   var o = jq_getStarter(term);
			   this.strter=o[0];
			},
		    //source:	zPath+"/common/getICD10data.php",
		    minLength: 2,
		    autoFocus: true,
		    focus: function( event, ui ) {return false;},
		    select: function( event, ui ) { if(ui.item.value!=""){
				var t = jq_extractLast( this.value ,2, this);
				var r = ""+ui.item.value;
				r=cn_adjustSearchValue(r,t[3]);
				if(typeof(this.strter)!="undefined" && this.strter!=""){r=$.trim(this.strter+" "+r);}
				this.value =  $.trim(""+t[0]+""+r+t[1]);
				$(this).triggerHandler("blur");
				return false;
			} }
		});
	};
	$( ""+wh ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta8(this);};});
}

//-- Type ahead --

//Superbill typeahead --

function sb_dx_typeahead(){
	if(typeof(zPath)!="undefined"&&zPath!=""){}else{zPath="";}
	var url  = zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=getICD10Data";
	$( ".dxallcodes, textarea[name^='elem_assessment_dxcode']" ).each(function(indx){ bind_autocomp($(this), url); });
}

function sb_addTypeAhead(){

	if(typeof(zPath)!="undefined"&&zPath!=""){}else{zPath="";}

	var ta7 = function(ota7){
	$( ota7 ).autocomplete({
	    //source: zPath+"/chart_notes/getSuperBillInfo.php?elem_formAction=cptdropdown",
	     source: function( request, response ) {
			$.ajax({
			url: zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=cptdropdown",
			dataType: "json",
			data: {
			term: request.term
			},
			success: function( data ) {
				response( $.map( data, function( item ) {
					var tmp_Lbl= tmp_Val="";
					if(typeof(item) == "object"){
						tmp_Lbl= item.label ? item.label : item.value;
						tmp_Val= item.value;
					}else{
						tmp_Lbl=item;
						tmp_Val=item;
					}

					//*
					return {
					label: tmp_Lbl,
					value: tmp_Val
					}
					//*/
				}));
			}
			});
		},
	    minLength: 2,
	    autoFocus: true,
	    focus: function( event, ui ) {return false;},
	    select: function( event, ui ) { if(ui.item.value!=""){ this.value=""+ui.item.value; this.onblur();} }

	});
	};
	$( ".cptcode" ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta7(this);};});


	var tmp = $("#hid_icd10").val();
	if(typeof(tmp)!="undefined" && tmp==1){

		//alert($( ":input[name^='elem_assessment_dxcode']" ).length);
		sb_dx_typeahead();

	}else{
		//*
		var ta8 = function(ota8){
		$( ota8 ).autocomplete({
		    source: zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=dxdropdown",
		    //source:	zPath+"/common/getICD10data.php",
		    minLength: 2,
		    autoFocus: true,
		    focus: function( event, ui ) {return false;},
		    select: function( event, ui ) { if(ui.item.value!=""){this.value=""+ui.item.value;this.onblur();} }
		});
		};
		$( ".dxallcodes, textarea[name^='elem_assessment_dxcode']" ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta8(this);};});
		//*/
	}

	var ta9 = function(ota9){
	$( ota9 ).autocomplete({
	    source: zPath+"/chart_notes/requestHandler.php?elem_formAction=TypeAhead&mode=moddropdown",
	    minLength: 1,
	    autoFocus: true,
	    focus: function( event, ui ) {return false;},
	    select: function( event, ui ) { if(ui.item.value!=""){this.value=""+ui.item.value;this.onblur();} }
	});
	};
	$( ".modcode" ).bind( "focus", function(event){if(!$(this).hasClass("ui-autocomplete-input")){ta9(this);};});

}
//Superbill typeahead --

// color typeahead --
if($.ui && $.ui.autocomplete){
$.ui.autocomplete.prototype._renderItem = function (ul, item) {//alert(item.label + " "+item.value)
	arr = item.label.split("~~");
	var clss ="";
	if(arr[1] == 'commu'){clss ="text-warning";}
	if(arr[1] == 'phy'){clss ="text-success";}
	if(arr[1] == 'dyn'){clss ="text-primary";}

	if(clss!=""){
	var term = this.term,
				formattedLabel = "<span class=\'"+clss+"\'>"+arr[0]+"</span>";
	}else{
	var term = this.term,
				formattedLabel = item.label;
	}

	/*
	else if(arr[1] == 'phy'){
	var term = this.term,
				formattedLabel = "<span style='color:green'>"+arr[0]+"</span>";
	}else if(arr[1] == 'dyn'){
	var term = this.term,
				formattedLabel = "<span style='color:blue'>"+arr[0]+"</span>";
	}else if(arr[1] == 'dx'){
	var term = this.term,
				formattedLabel = "<span>"+arr[0]+"</span>";
	*/

	return $( "<li></li>" )
		.data( "item.autocomplete", item )
		.append( "<a>" + formattedLabel + "</a>" )
		.appendTo( ul );
};
}
// color typeahead --
