//reffering phy--

$(document).ready(function(){
	/*--showing details of ref.phy, pcp, co-managed physician--*/
	top.$('#spanMoreCoMangPhy, #spanMoreRefPhy, #spanMorePCP').unbind("click");
	top.$('#spanMoreCoMangPhy, #spanMoreRefPhy, #spanMorePCP').click(function(){
		var refPhyId = "";
		var multi = "";
		var spanID = $(this).attr('id');
		var patId = sess_pt;  //$('#hidSessionPatId').val();
		if(spanID == 'spanMoreCoMangPhy'){
			multi = "2";
		}
		else if(spanID == 'spanMoreRefPhy'){
			multi = "1";
		}
		else if(spanID == 'spanMorePCP'){
			multi = "3,4";
		}
		
		//--
		/*
		if($("#ref_pcp_coman_details_span").length<=0){			
			var htm=""+							
					"<span id=\"ref_pcp_coman_details_span\" cla11ss=\"div_popup white border padd5 hide dvMultiRefPhy\" style=\"position:absolute;top:0px;left:0px;width:100px;height:100px;background-color:white;z-index:10000;\">"+
					"    <span class=\"closeBtn\" onClick=\"hideDetail();\"></span>"+
					"    <span id=\"ref_pcp_coman_details_span_inner\"></span>"+
					"</span>"+					
					"";
			$("body").append(htm);		
		}
		*/
		//--
		//return;	
		
		top.$('#spanMoreCoMangPhy, #spanMoreRefPhy, #spanMorePCP').popover("destroy");
		//alert(multi)
		$.ajax({
			url: zPath+"/patient_info/ajax/insurance/ajax.php?action=refPhyDetails&refPhyId="+refPhyId+"&multi="+multi+"&id="+patId,
			dataType: 'json',
			success: function(data){
				//alert(r);
				//document.write(r);
				//return;
				var r="";
				if(data && data.data){ r=""+data.data; }
				
				if(r==''){
					//$('#ref_pcp_coman_details_span').hide();
					top.$("#"+spanID).popover("destroy");
				}else{					
					//$('#ref_pcp_coman_details_span').html(r);
					//$('#ref_pcp_coman_details_span_inner').html(r);
					
					//$('#ref_pcp_coman_details_span').show();
					
					
					
					var title_r="", content_r="";
					var ar=r.split("~||~");
					if(typeof(ar[0])!="undefined"){title_r=ar[0];}
					if(typeof(ar[1])!="undefined"){content_r=ar[1];content_r = content_r.replace(/\n/g, '<br/>');}					
					
					top.$("#"+spanID).popover({title: ''+title_r, content: ''+content_r, html: true, placement:"bottom"});
					top.$("#"+spanID).popover("show");
				}
			}
		});
	});
	
	/*
	top.$('#spanMoreCoMangPhy, #spanMoreRefPhy, #spanMorePCP').mousemove(function(e){
		src = $(this); tgt = $('#ref_pcp_coman_details_span'); ofs = src.offset(); l = ofs.left; t = ofs.top;
		var spanID = $(this).attr('id');
		if(spanID == 'spanMoreCoMangPhy'){
			tgt.css({top:t, left:l - 148});
		}
		else if(spanID == 'spanMoreRefPhy'){
			tgt.css({top:t, left:l - 80});
		}
		else if(spanID == 'spanMorePCP'){
			tgt.css({top:t, left:l - 110});
		}
		//tgt.css({top:t, left:l - 150});
		//, width:src.width()+25
	});
	*/
	
});

function loadPhysicians(){} ///*//dummyfunctionfor showMultiPhy() ;*/
function hideDetail(){
	//$('#ref_pcp_coman_details_span').hide();
	//$('#ref_pcp_coman_details_span_inner').html(''); 
}
var refPhyName = new Array();
var refPhyNameID = new Array();
function showMultiPhy(op, phyType){
	hideDetail();
	op = op || 0;
	phyType = phyType || 0;
	
	//--
	if($("#divMultiRefPhy").length<=0){
		
		var htm=""+
				"<div id=\"divMultiRefPhy\" class=\"section mt10 m5 dvMultiRefPhy\"  ></div>"+
				"<div id=\"divMultiPCPDemo\" class=\"section mt10 m5 dvMultiRefPhy\" ></div>"+
				"<div id=\"divMultiCoPhy\" class=\"section mt10 m5 dvMultiRefPhy\" ></div>"+
				//"<span id=\"ref_pcp_coman_details_span\" class=\"div_popup white border padd5 hide dvMultiRefPhy\" style=\"position:absolute;top:0px;left:0px;width:100px;height:100px;background-color:white;\">"+
				//"    <span class=\"closeBtn\" onClick=\"hideDetail();\"></span>"+
				//"    <span id=\"ref_pcp_coman_details_span_inner\"></span>"+
				//"</span>"+					
				"";
		$("body").append(htm);
		$("#divMultiRefPhy, #divMultiPCPDemo, #divMultiCoPhy").draggable({handle:".modal-title"});	
	}
	//--
	
	if(op == 1){
		var arrRefPhy = new Array();
		var arrRefPhyHid = new Array();
		var arrCoPhy = new Array();
		var arrCoPhyHid = new Array();
		var arrPCPDemoPhy = new Array();
		var arrPCPDemoHid = new Array();
		var strRefPhy = "";
		var strCoPhy = "";
		var strPCPDemoPhy = "";
		var strRefPhyHid = "";
		var strCoPhyHid = "";
		var strPCPDemoHid = "";
		top.show_loading_image("show");
		
		var tmp_txt="", tmp_hidden="";
		
		if(phyType == 1 || phyType == 12){
			tmp_txt="txtRefPhyArr[]";
			tmp_hidden="hidRefPhyArr-";
		}else if(phyType == 2){
			tmp_txt="txtCoPhyArr[]";
			tmp_hidden="hidCoPhyArr-";
		}else if(phyType == 4){
			tmp_txt="txtPCPDemoArr[]";
			tmp_hidden="hidPCPDemoArr-";
		}		
		
		if(tmp_txt != "" && tmp_hidden != ""){
			if(document.getElementsByName(""+tmp_txt)){
				var objRefPhyArr = document.getElementsByName(""+tmp_txt);
				for(var i = 0; i < objRefPhyArr.length; i++){
					var objRefPhyArrID = objRefPhyArr[i].id;
					var arrRefPhyArrID = objRefPhyArrID.split("-");
					var hidRefPhyArrID = ""+tmp_hidden + arrRefPhyArrID[1];
					if((document.getElementById(objRefPhyArrID)) && (document.getElementById(hidRefPhyArrID))){
						arrRefPhy[i] = document.getElementById(objRefPhyArrID).value;
						arrRefPhyHid[i] = document.getElementById(hidRefPhyArrID).value;
					}							
				}
				if(arrRefPhy.length > 0){
					strRefPhy = arrRefPhy.join("!~#~!");
					strRefPhyHid = arrRefPhyHid.join("!~#~!");
					//alert("1"+strRefPhy);
				}
			}
		}	
		
		
		$.ajax({
				url: zPath+"/patient_info/common/muti_phy.php?mode=get&phyType="+phyType+"&strRefPhy="+strRefPhy+"&strRefPhyHid="+strRefPhyHid+"&strCoPhy="+strCoPhy+"&strCoPhyHid="+strCoPhyHid+"&strPCPDemoPhy="+strPCPDemoPhy+"&strPCPDemoHid="+strPCPDemoHid+"&callFrom=WV",
				success: function(respRes){
					//alert(respRes);
					var tmp_txt_arr="", tmp_hid_arr="", tmp_span_id="", tmp_multi_cur="",
						tmp_multi_ref="divMultiRefPhy", tmp_multi_co="divMultiCoPhy", tmp_multi_pcp="divMultiPCPDemo";
						
					if(phyType == 1 || phyType == 12){
						tmp_multi_cur="divMultiRefPhy";
						tmp_span_id="spanDetailRefPhy";
					}else if(phyType == 2){
						tmp_multi_cur="divMultiCoPhy";
						tmp_span_id="spanDetailCoMangPhy";
					}else if(phyType == 4){
						tmp_multi_cur="divMultiPCPDemo";
						tmp_span_id="spanDetailPCP";
					}
					
					//
					if(tmp_multi_cur!=""){
						var arrResp = respRes.split("!~-1-~!");
						var arrTemp = arrResp[1].split("~-~");
						
						for(var a = 0; a <= arrTemp.length; a++){
							refPhyName[a] = arrTemp[a];
						}
						var arrTemp = arrResp[2].split("~-~");
						
						for(var a = 0; a <= arrTemp.length; a++){
							refPhyNameID[a] = arrTemp[a];
						}
						
						document.getElementById(""+tmp_multi_ref).style.zIndex = 1;
						document.getElementById(""+tmp_multi_co).style.zIndex = 1;
						document.getElementById(""+tmp_multi_pcp).style.zIndex = 1;
						document.getElementById(""+tmp_multi_cur).innerHTML = arrResp[0];
						document.getElementById(""+tmp_multi_cur).style.zIndex = 1000;	
						
						var selectedEffect = "blind";
						var offset = top.$("#"+tmp_span_id).offset();
						var x = offset.left-250;
						var y = $(window).scrollTop()+20;						
						$("#"+tmp_multi_cur).css({'left':""+x+"px",'top':""+y+"px","z-index":"1000"});
						$("#"+tmp_multi_cur).show(selectedEffect,"", 500);
						
						multiphy_typeahead(phyType);	
							
					}
					
					top.show_loading_image("hide"); 
				}
			});
	}
	else if(op == 0){
		//document.getElementById("divMultiPhy").style.display = "none";
		var selectedEffect = "blind";
		if(phyType == 1 || phyType == 12){
			$("#divMultiRefPhy").hide(selectedEffect,"", 500);
			//document.getElementById("spanAddRefPhy").style.display = "block";
		}
		else if(phyType == 2){
			$("#divMultiCoPhy").hide(selectedEffect,"", 500);
			//document.getElementById("spanAddCoMangPhy").style.display = "block";
		}
		else if(phyType == 4){
			$("#divMultiPCPDemo").hide(selectedEffect,"", 500);
			//document.getElementById("spanAddPCPDemo").style.display = "block";
		}
	}
	else if(op == 2){
		top.show_loading_image("show");
		var selectedEffect = "blind";
		
		var tmp_txt_arr="", tmp_hid_arr="", tmp_span_id="", tmp_multi_cur="", tmp_hid_arr_id="", tmp_del_hid="",
			tmp_multi_ref="divMultiRefPhy", tmp_multi_co="divMultiCoPhy", tmp_multi_pcp="divMultiPCPDemo";
		
		if(phyType == 1 || phyType == 12){
			tmp_txt_arr="txtRefPhyArr[]";
			tmp_hid_arr="hidRefPhyArr-";
			tmp_hid_arr_id="hidRefPhyId";
			tmp_del_hid="hidDeleteRefPhy";
			tmp_multi_cur="divMultiRefPhy";
			strTxtUsrArr="strTxtRefPhyArr";
			strHidUsrIdID="strHidRefPhyIdID";
			strHidUsrArrID="strHidRefPhyArrID";
			hidDeleteUsrVal="hidDeleteRefPhyVal";
			
		}else if(phyType == 2){
			tmp_txt_arr="txtCoPhyArr[]";
			tmp_hid_arr="hidCoPhyArr-";
			tmp_hid_arr_id="hidCoPhyId";
			tmp_del_hid="hidDeleteCoPhy";
			tmp_multi_cur="divMultiCoPhy";
			strTxtUsrArr="strTxtCoPhyArr";
			strHidUsrIdID="strHidCoPhyIdID";
			strHidUsrArrID="strHidCoPhyArrID";
			hidDeleteUsrVal="hidDeleteCoPhyVal";
			
			
		}else if(phyType == 4){
			tmp_txt_arr="txtPCPDemoArr[]";
			tmp_hid_arr="hidPCPDemoArr-";
			tmp_hid_arr_id="hidPCPDemoId";
			tmp_del_hid="hidDeletePCPDemo";
			tmp_multi_cur="divMultiPCPDemo";	
			strTxtUsrArr="strTxtPCPDemoArr";
			strHidUsrIdID="strHidPCPDemoIdID";
			strHidUsrArrID="strHidPCPDemoArrID";
			hidDeleteUsrVal="hidDeletePCPDemoVal";	
		}

		if(tmp_multi_cur!=""){
			var strTxtRefPhyArr = "";
			var strHidRefPhyArrID = "";	
			var strHidRefPhyIdID = "";				
			if(document.getElementsByName(""+tmp_txt_arr)){
				var objRefPhyArr = document.getElementsByName(""+tmp_txt_arr);
				for(var i = 0; i < objRefPhyArr.length; i++){
					var objRefPhyArrID = objRefPhyArr[i].id;
					var arrRefPhyArrID = objRefPhyArrID.split("-");
					var hidRefPhyArrID = ""+tmp_hid_arr + arrRefPhyArrID[1];
					var hidRefPhyIdID = ""+tmp_hid_arr_id + arrRefPhyArrID[1];
					//alert(hidRefPhyIdID);
					if((document.getElementById(objRefPhyArrID)) && (document.getElementById(hidRefPhyArrID))){
						strTxtRefPhyArr += document.getElementById(objRefPhyArrID).value + "!$@$!";
						strHidRefPhyArrID += document.getElementById(hidRefPhyArrID).value + "!$@$!";
						if(document.getElementById(hidRefPhyIdID)){
							strHidRefPhyIdID += document.getElementById(hidRefPhyIdID).value + "!$@$!";
						}
					}							
				}
				//alert(strTxtRefPhyArr);
			}
			var hidDeleteRefPhyVal = document.getElementById(""+tmp_del_hid).value;
			
			$.ajax({
				url: zPath+"/patient_info/common/muti_phy.php?mode=save&phyType="+phyType+"&"+strTxtUsrArr+"="+strTxtRefPhyArr+"&"+strHidUsrIdID+"="+strHidRefPhyIdID+"&"+strHidUsrArrID+"="+strHidRefPhyArrID+"&"+hidDeleteUsrVal+"="+hidDeleteRefPhyVal,
				success: function(respRes){
					//alert(respRes);							
					var arrRespRes = respRes.split("-!-");
					if(arrRespRes[0] == "DONE"){
						$("#"+tmp_multi_cur).hide(selectedEffect,"", 500);
						//document.getElementById("spanAddRefPhy").style.display = "block";
					}
					top.show_loading_image("hide"); 
					set_refer_phy_tbar(1);
				}
			});
		}
	}	
}

function add_phy_row(add_image_id, del_image_id, intCounter, phyType){
	
	var objDelImg = $("#"+del_image_id);
	var objAddImg = $("#"+add_image_id);
	
	if(objAddImg){ objAddImg.addClass('hidden') }			
	if(objDelImg){ objDelImg.removeClass('hidden');	}
	
	var intCounterTemp = parseInt(intCounter) + 1;
	var divTrTag = document.createElement("div");
	divTrTag.id = "divTR" + "-" + phyType + "-" + intCounterTemp
	divTrTag.className = "col-xs-12";
	divTrTag.style.marginBottom = "5px";
	
	var divTDTag1 = document.createElement("div");
	divTDTag1.className = "col-xs-2 text-center";
	//divTDTag1.style.width = "55px";
	divTDTag1.innerHTML = intCounterTemp;			
	divTrTag.appendChild(divTDTag1);
	
	var divTDTag2 = document.createElement("div");
	divTDTag2.className = "col-xs-9";
	
	if(phyType == 1 || phyType == 12){
		var txtId = "txtRefPhyArr-"+intCounterTemp;
	}
	else if(phyType == 2){
		var txtId = "txtCoPhyArr-"+intCounterTemp;
	}
	else if(phyType == 4){
		var txtId = "txtPCPDemoArr-"+intCounterTemp;
	}
	
	var txtBox = document.createElement("input");
	txtBox.type = "text";
	//txtBox.name = "txtPhyArr[]";
	if(phyType == 1 || phyType == 12){
		txtBox.name = "txtRefPhyArr[]";
	}
	else if(phyType == 2){
		txtBox.name = "txtCoPhyArr[]";
	}
	else if(phyType == 4){
		txtBox.name = "txtPCPDemoArr[]";
	}
	txtBox.id = txtId;
	txtBox.value = "";
	txtBox.className = "form-control";
	//txtBox.style.width = "140px";
	divTDTag2.appendChild(txtBox);
	
	if(phyType == 1 || phyType == 12){
		var hidId = "hidRefPhyArr-"+intCounterTemp;
	}
	else if(phyType == 2){
		var hidId = "hidCoPhyArr-"+intCounterTemp;
	}
	else if(phyType == 4){
		var hidId = "hidPCPDemoArr-"+intCounterTemp;
	}
	var hidBox = document.createElement("input");
	hidBox.type = "hidden";
	//hidBox.name = "hidCoPhyArr[]";
	if(phyType == 1 || phyType == 12){
		hidBox.name = "hidRefPhyArr[]";
	}
	else if(phyType == 2){
		hidBox.name = "hidCoPhyArr[]";
	}
	else if(phyType == 4){
		hidBox.name = "hidPCPDemoArr[]";
	}
	hidBox.id = hidId;
	hidBox.value = "";
	divTDTag2.appendChild(hidBox);
	
	divTrTag.appendChild(divTDTag2);
	
	var divTDTag3 = document.createElement("div");
	divTDTag3.className = "col-xs-1";
	var imgDelId = "imgDel" + "-" + phyType + "-" + intCounterTemp;
	var imgAddId = "imgAdd" + "-" + phyType + "-" + intCounterTemp;
	var strImgHTML = "<span id=\""+imgDelId+"\" name=\""+imgDelId+"\" class=\"pointer hidden\" onClick=\"del_phy_row('"+imgDelId+"','"+intCounterTemp+"', '','"+phyType+"');\" ><i class=\"glyphicon glyphicon-remove\"></i></span>";
			
	strImgHTML += "<span id=\""+imgAddId+"\" name=\""+imgAddId+"\" onClick=\"add_phy_row('"+imgAddId+"','"+imgDelId+"', '"+intCounterTemp+"', '"+phyType+"');\" class=\"pointer\" ><i class=\"glyphicon glyphicon-plus\"></i></span>";
	
	//alert(strImgHTML);
	divTDTag3.innerHTML = strImgHTML;
	
	divTrTag.appendChild(divTDTag3);
	if(phyType == 1 || phyType == 12){
		document.getElementById("divMultiPhyInner1").appendChild(divTrTag);
	}
	else if(phyType == 2){
		document.getElementById("divMultiPhyInner2").appendChild(divTrTag);
	}
	else if(phyType == 4){
		document.getElementById("divMultiPhyInner4").appendChild(divTrTag);
	}
	//new actb(document.getElementById(txtId),refPhyName,"","",document.getElementById(hidId),refPhyNameID);
	//txtBox.addEventListener("keyup",function(){reff_multi_add_popup(txtBox,hidId,'WV')});
	//txtBox.addEventListener("focus",function(){reff_multi_add_popup(txtBox,hidId,'WV')});	
	multiphy_typeahead(phyType);
	document.getElementById(txtId).focus();
}

function del_phy_row(del_image_id, intCounter, intPhyIdDB, phyType){	
	var objDelImg = $("#"+del_image_id);
	intPhyIdDB = intPhyIdDB || 0;
	//var divTrTag = "divTR" + intCounter;
	var divTrTag = "divTR" + "-" + phyType + "-" + intCounter;	
	if((intPhyIdDB > 0) && (phyType == 1 || phyType == 12)){				
		document.getElementById("hidDeleteRefPhy").value += intPhyIdDB+'-';
	}
	else if((intPhyIdDB > 0) && phyType == 2){				
		document.getElementById("hidDeleteCoPhy").value += intPhyIdDB+'-';
	}
	else if((intPhyIdDB > 0) && phyType == 4){				
		document.getElementById("hidDeletePCPDemo").value += intPhyIdDB+'-';
	}
	if(document.getElementById(divTrTag)){
		var divType = "divMultiPhyInner" + phyType;
		var objMainDiv = document.getElementById(divType);
		objMainDiv.removeChild(document.getElementById(divTrTag));		
	}
}
function reff_multi_add_popup (){}
/*
function reff_multi_add_popup(ele,hidd,callFrom,hidd2,faxID2,faxRefNameId){
	callFrom = callFrom || '';
	if(callFrom == "WV" || callFrom == "popup"){
		position = $(ele).position();
		left_margin = 10;
		top_margin = 10;
	}
	else{
		position = $(ele).offset();
		left_margin = position.left;
		top_margin = position.top;
	}
	
	reff_id = $(hidd).val();
	$("#div_reff_add").remove();
	if($("#div_reff_add").length <= 0 && reff_id > 0){
		ele_val = $(ele).val();
		arrName = ele_val.split(",");
		if(arrName.length < 2)return;
		if(opener != "undefined" && opener != null)opener.top.show_loading_image('show');
		else top.show_loading_image('show');
		if(callFrom == "WV" || callFrom == "popup"){
			$("<div id='div_reff_add' class='section mt10 m5' style='display:none; position:absolute; margin-left:"+left_margin+"px; margin-top:"+top_margin+"px;width:250px; z-index:10001;clear:both' ><div id='div_reff_add_header' class='section_header boxhead alignLeft' style='cursor:move;'><span id='reff_add_close' class='closeBtn'></span>Choose Address</div><div id='div_reff_add_content' style='background-color:white;overflow:hidden;overflow-y:auto;max-height:200px;'></div></div>").insertAfter(ele);
		}else{
			if(opener != "undefined" && opener != null){opener.top.show_loading_image('hide');}
			var str = "<div id='div_reff_add' class='section mt10 m5' style='display:none; position:absolute; left:"+left_margin+"px; top:"+top_margin+"px;width:250px; z-index:10001; clear:both' ><div id='div_reff_add_header' class='section_header boxhead alignLeft' style='cursor:move;'><span id='reff_add_close' class='closeBtn'></span>Choose Address</div><div id='div_reff_add_content' style='background-color:white;overflow:hidden;overflow-y:auto;max-height:200px;'></div></div>";
			$('body').append(str);
		}
		$("#reff_add_close").bind("click",function(){$('#div_reff_add').hide();});
		if($("#div_reff_add").draggable != "undefined" && typeof($("#div_reff_add").draggable) != "undefined")
		$("#div_reff_add").draggable({handle:"section_header"});
		
	}
	get_reff_address(reff_id, hidd, hidd2,faxID2,faxRefNameId);
	winWidth = $(window).width();
	winHeight = $(window).height();
	leftMargin = left_margin;
	topMargin = top_margin;
	offsetLeft =  leftMargin + 250;
	//offsetTop =  topMargin + $("#div_reff_add").height();
	offsetTop =  topMargin + 200;
	if(offsetLeft+20 > winWidth){
		left_margin = left_margin - (offsetLeft - winWidth) - 10;
		$("#div_reff_add").css({left:left_margin});
	}
	if(offsetTop+30 > winHeight){
		top_margin = top_margin - (offsetTop - winHeight) - 30;
		$("#div_reff_add").css({top:top_margin});
	}
	//alert("top "+position.top + "\n offsetTop "+offsetTop+" \n winHeight "+winHeight)
}
*/
/*
function get_reff_address(reff_id, hidd, hidd2, faxID2,faxRefNameId){
	if(reff_id != "" && reff_id > 0){
		hidd_id = $(hidd).attr('id');
		hidd_id2 = $(hidd2).attr('id');
		fax_id2 = $(faxID2).attr('id');
		fax_ref_name_id = $(faxRefNameId).attr('id');
		$.ajax({url:zPath+"/patient_info/common/reff_phy_add.php",
				type: "POST",
				data: "id="+reff_id+"&hidd_id="+hidd_id+"&hidd_id2="+hidd_id2+"&fax_id2="+fax_id2+"&fax_ref_name_id="+fax_ref_name_id,
				success:function(r){
					if(r != ""){
						//a = window.open(); a.document.write(r)
						$("#div_reff_add_content").html(r);
						$("#div_reff_add").show();
						if(opener != "undefined" && opener != null)opener.top.show_loading_image('hide');
						else top.show_loading_image('hide');
					}
					if(opener != "undefined" && opener != null)opener.top.show_loading_image('hide');
					else top.show_loading_image('hide');
				}
		});
	}
}
*/
//reffering phy--
