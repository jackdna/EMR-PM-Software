//Amsler Grid -----
function ag_setWnlEyeFlag( op, op2 ){		
	var oWnl = gebi("wnl_flag_amsler");//if yes means abn no means wnl 
	var seOd = gebi("elem_wnlOd_amsler");
	var seOs = gebi("elem_wnlOs_amsler");

	//it make setting 
	if( op == "1" ){			
		if((oWnl.value == "yes")){			
			var obj = ag_getAppForWnl();	
			seOd.value = ( obj.Od != true ) ? "1" : "0";
			seOs.value = ( obj.Os != true ) ? "1" : "0";
		}else if( oWnl.value == "no" ){		
			seOd.value = "1";
			seOs.value = "1";
		}else{
			seOd.value = "0";
			seOs.value = "0";
		}
	}		
	//
	
	//gebi("flagWnlOd").style.display = (oWnl.value != "no" && seOd.value == "1") ? "block" : "none";
	//gebi("flagWnlOs").style.display = (oWnl.value != "no" && seOs.value == "1") ? "block" : "none";
	
	if(oWnl.value != "no" && seOd.value == "1"){
		$("#flagWnlOd_ag").removeClass("hidden");
	}else{
		$("#flagWnlOd_ag").addClass("hidden");
	}
	
	if(oWnl.value != "no" && seOs.value == "1"){
		$("#flagWnlOs_ag").removeClass("hidden");
	}else{
		$("#flagWnlOs_ag").addClass("hidden");
	}
	
	//main 
	//if( op2 == "1" ){
	
	
	
	var imgObj = gebi('flagImg_ag');
	if(oWnl.value == "yes" || (seOd.value == "1" && seOs.value == "1")){		
		//imgObj.style.display = "block";	

		$(imgObj).removeClass("hidden").css({'display':'inline-block'});
	}else{
		//imgObj.style.display = "none";
		$(imgObj).addClass("hidden").css({'display':'none'});
	}	
	
}
function ag_getAppForWnl(){
	var flag=flagOd=flagOs=false;
	if(isHTML5OK=="1"){
		var strod = gebi('sig_dataapp_ams_od').value;
		if(strod!=""){			
			flagOd=true;
		}else{
			flagOd=false;
		}		
		
		var stros = gebi('sig_dataapp_ams_os').value;
		if(stros!=""){
			flagOs=true;
		}else{
			flagOs=false;
		}
		
	}else{
		/*
		var getOdCords = getCoords('app_ams_od');
		var getOsCords = getCoords('app_ams_os');
		var imgObj = gebi('flagImg');		
		
		var flagOd=checkSign(getOdCords);
		var flagOs=checkSign(getOsCords);
		*/
	
	}
	if(flagOd || flagOs){	
		flag=true;
	}else{
		flag=false;
	}
	
	return {"Exam":flag,"Od":flagOd,"Os":flagOs};
}
function ag_setWnlFlag(){
	//var getVal = gebi('elem_doctorName').value;
	//var getTxtVal = gebi('elem_notes').value;
	var wOd = gebi("elem_wnlOd_amsler");
	var wOs =  gebi("elem_wnlOs_amsler");
	var ownl = gebi('wnl_flag_amsler');
	
	var imgObj = gebi('flagImg_ag');
	var obj = ag_getAppForWnl();
	var ret = false;

	//alert(""+obj.Exam);
	

	 if(obj.Od == true){
		wOd.value = "0";
	 }else{ // if no change set to wnl
		wOd.value = "1";
	 }
	 
	 if(obj.Os == true){
		wOs.value = "0";
	 }else{ // if no change set to wnl
		wOs.value = "1";			 
	 }
	
	if(obj.Exam==true){
		//imgObj.style.display = 'block';
		//imgObj.src = 'images/flag_yellow.png';
		$(imgObj).addClass("pos_flg").removeClass("hidden wnl_flg");
		ownl.value = 'yes';   // WNL_flag = yes => Positive		
		ret = true;
	}else if(obj.Exam==false){
		
		if(wOd.value == "1" && wOs.value == "1"){
			ownl.value = 'no';
		}else{
			ownl.value = '';
		}
	}
	//
	ag_setWnlEyeFlag( "0" );
	ag_clear_grey_bg();
	
	return ret;
}

//called from WNL button
function checkwnl_amsler(){
	var imgObj = gebi('flagImg_ag');
	
	var oWnl = gebi("wnl_flag_amsler");
	var seOd = gebi("elem_wnlOd_amsler");
	var seOs = gebi("elem_wnlOs_amsler");
	var obj = ag_getAppForWnl();

	if(obj.Exam == true){
		//WNL Full is no possible
		
		//Check for eye only
		if(obj.Od != true)	{
			
			//set wnl
			seOd.value = "1";
		}

		if(obj.Os != true)	{
			
			//set wnl
			seOs.value = "1";
		}			
		
		oWnl.value = "yes";
		//imgObj.src = 'images/flag_yellow.png';
		$(imgObj).addClass("pos_flg").removeClass("wnl_flg");
		
	}else{
		//Full is possible and toggle
		if(seOd.value == "0" || seOs.value == "0"){
			seOd.value = "1";
			seOs.value = "1";
		}else{
			seOd.value = "0";
			seOs.value = "0";
		}
		
		//main Wnl
		if(seOd.value == "1" && seOs.value == "1"){
			oWnl.value = "no";	
			//imgObj.src = 'images/flag_gn2.png';
			$(imgObj).addClass("wnl_flg").removeClass("pos_flg");
		}else{
			oWnl.value = "";
		}
	}	
	ag_setWnlEyeFlag( "0" );
	ag_clear_grey_bg();	
	
}
function funSave_ag(){
	var strsave=$("#frm_amsler_grid").serialize(); 
	strsave+="&elem_saveForm=save_ams_grid";
	strsave+="&savedby=ajax";	
	$.post("saveCharts.php", strsave, function(data) {
			$("#agModal .close").trigger('click');
			loadExamsSummary("amsgrid");
		},'json');
}
//set userName
function setUserName_ag(){
	var obj = { "id":authUserID,"name":authUserNM };
	var ocid = $("#elem_doctorName_amsler")[0];
	var ocname = $("#elem_doctorName_show_amsler")[0];
	
	if(ocid && ocname){			
		ocid.value = obj.id;			
		ocname.value = obj.name;
	}
}
function funReset_ag(){
	getClear('app_ams_od');
	getClear('app_ams_os');
	
	var e = document.frm_amsler_grid.elements;
	var l = e.length;
	for(var i=0;i<l;i++)
	{
		if(e[i].type == "checkbox"){
			e[i].checked = false;		
		}else if(e[i].type == "textarea"){
			e[i].value = "";
		}
		else if(e[i].type == "radio"){
			e[i].checked = false;
		}
		else if(e[i].type == "text"){
			e[i].value = "";
		}else if( (e[i].name == "elem_amslerOs") || (e[i].name == "elem_amslerOd") ){				
			e[i].value = "";
		}else if( (e[i].name == "wnl_flag_amsler") ){
			e[i].value = "";
		}else if( (e[i].name == "elem_wnlOd_amsler") || (e[i].name == "elem_wnlOs_amsler") ){
			e[i].value = "0";
		}
	}
	//setWnlEyeFlag( "1", "1" );
	ag_setWnlEyeFlag("0");
	//getClear('app_signature');
	setUserName_ag();
	
}
function setPrevValues_ag(){	
	$("#agModal, .modal-backdrop").remove();
	openPW('AG','','prevVal=1');	
}


//Amsler Grid -----
