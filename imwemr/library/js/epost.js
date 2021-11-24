

var _epostX=350;
var _epostY=100;
function epostpopTest(){
	var i = parseInt(gebi('epost_insertID').value);
	gebi('epost_insertID').value = i+1;
	addEPost(i);
	var oReq = gebi('evaluationEPostDiv'+i);
	if(oReq){
		oReq.style.display = 'block';
		var posX = _epostX;
		var posY = _epostY;
		//var thisScrollTop = $(window).scrollTop(); //document.body.scrollTop;
		
		//posY += parseInt(thisScrollTop);		
		$('#evaluationEPostDiv'+i).css({"top":posY, "left":posX});		
		
		_epostX+=10;
		_epostY+=10;
	}
}

//--------------

function saveEpost(i, examName){
	var getTexts = $("#eposting"+i).val();
	var epos = $("#evaluationEPostDiv"+i).position();
	var edb = eval("("+$("#evaluationEPostDiv"+i).attr("data-epost")+")");
	if(typeof(edb)!="undefined" && typeof(edb.dbId)!="undefined"){
		var edbId = edb.dbId;
	}else{
		edbId = "";
	}

	var strPrePhrase = "";
	$("#evaluationEPostDiv"+i+" :checked").each(function(){strPrePhrase+=$(this).val()+"*|*";});

	if((getTexts != '' || strPrePhrase != "") && epos.left !='' && epos.top != ''){
		examName = (typeof examName == "undefined") ? "" : examName;
		var url1=zPath+"/chart_notes/saveCharts.php";
		$.get(url1,{ q: getTexts, name: "evaluationEPostDiv"+i,left_div:epos.left+"px",
					 top_div:epos.top+"px",prephrase:strPrePhrase,examName:examName,elem_dbId:edbId,
					elem_saveForm:"EPOST", op:"add"},
			function(data){$("#evaluationEPostDiv"+i).attr("data-epost","{'dbId':"+data+"}");});
	}
}

	function addEPost( i ){
		if( isNaN(i) ){
			return;
		}
		if(typeof(authUserNM)=="undefined")authUserNM="";
		if(typeof(epost_crDt)=="undefined")epost_crDt="";
		
		var left = "350px";
		var ttop = "100px";
		var disEpost = "none";
		var attrDis = "0";
		var examNm = epost_examNm;
		var ePostData = "";
		var str = "";
		str+=	""+
			"<div class=\"epost panel panel-success\" style=\"left:"+left+"; top:"+ttop+"; display:"+disEpost+";\" id=\"evaluationEPostDiv"+i+"\" "+
				"attrDis=\""+attrDis+"\"  >"+
					"<div class=\"panel-heading\" onMouseUp=\"saveEpost("+i+",'"+examNm+"');\">"+
					"ePostIt : "+
					""+authUserNM+" ("+epost_crDt+")"+
					"<span class=\"glyphicon glyphicon-remove pull-right cur_hnd\" onClick=\"deleteEPost('"+i+"','"+examNm+"');\"></span>"+
					//"<button type=\"button\" class=\"close\" aria-label=\"Close\" onClick=\"deleteEPost('"+i+"','"+examNm+"');\">"+
					//"<span aria-hidden=\"true\">&times;</span>"+
					//"</button>"+
				"</div><div class=\"panel-body\">";
				for(var x in arrEpost){
		 
		str+=		""+
						"<input type=\"checkbox\" name=\"pre_phrase"+i+"\" id=\"pre_phrase"+i+"_"+x+"\"  value=\""+arrEpost[x]+"\" class=\"frcb\" "+
							"onClick=\" saveEpost("+i+",'"+examNm+"');\"  ><label for=\"pre_phrase"+i+"_"+x+"\" class=\"frcb\">"+arrEpost[x]+"</label><br/>"+
					"";
				}
		str+=		""+
					""+
						"<textarea name=\"eposting\" id=\"eposting"+i+"\" "+
								"onBlur=\"saveEpost("+i+",'"+examNm+"');\" class=\"form-control\" onkeyup=\"epost_setTaPlanHgt(this);\">"+ePostData+"</textarea>"+
					""+
				""+
			"</div><div class=\"panel-footer\"></div>"+
			"</div>";
			//$("#conEpost").append(str);
			$("body").append(str);	
			//By Ram To Make EPOST DIVDRAGABLE in SAFARI
			$("#evaluationEPostDiv"+i).draggable();
			if(typeof(epost_addTypeAhead)!="undefined"){ epost_addTypeAhead(); }	
	}
	
	function epost_setTaPlanHgt(o){		
		var num = o.id;
		var x = $("#"+num)[0].scrollHeight;
		var y = (typeof(x)!="undefined" && x>40) ? x : 40;		
		$("#"+num).attr("style", "height: "+y+"px !important");			
	}

	function deleteEPost(i,examName){
		var edb = eval("("+$("#evaluationEPostDiv"+i).attr("data-epost")+")");
		if(typeof(edb)!="undefined" && typeof(edb.dbId)!="undefined"){
			var edbId = edb.dbId;
		}else{
			edbId = "";
		}

		$("#evaluationEPostDiv"+i).remove();
		
		if(edbId!=""){
			examName = (typeof examName == "undefined") ? "" : examName;
			var url2=zPath+"/chart_notes/saveCharts.php";
			$.get(url2,{del:"evaluationEPostDiv"+i,examName:examName,elem_dbId:edbId,elem_saveForm:"EPOST", op:"delete"});
		}
	}
	