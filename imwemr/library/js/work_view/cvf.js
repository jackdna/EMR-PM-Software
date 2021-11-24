//CVF --------
function setWnl_cvf(){
	$("#cvfModal :input").each(function(){		
		if(this.type=="checkbox"){			
			this.checked = (this.name=="elem_fullOs" || this.name=="elem_fullOd") ? true : false;
		}else if(this.type=="textarea"){
			this.value = "";
		}
	});
	getClear('app_cvf_os_drawing');
	getClear('app_cvf_od_drawing');
	var o = $("#elem_wnl_cvf");
	var v = (o.val() == "Yes") ? "No" : "Yes";
	o.val(v);	
}
function setImgText(obj,id,dir){
	if((obj.checked == true) && (obj.name == 'elem_full'+dir)){
		$("#cvfModal :input").each(function(){	
			if((this.type=='checkbox') && ((this.name.indexOf(dir)!=-1))){ this.checked=false;}
		});
		$("#elem_full"+dir).prop("checked", true);
	}else{
		$("#elem_full"+dir).prop("checked", false);
	}
}

//BL
function setBL_cvf(){
	$("#cvfModal :input").each(function(){
		if((this.name == "elem_cvfOdDrawing") || (this.name == "elem_cvfOsDrawing")){ 
			//
		}else{
			if(this.name.indexOf("Od") != -1){		
				var  t = this.name.replace(/Od/,"Os");
				var to1 = $("#"+t);
				if(to1.length>0){
					to=to1[0];
					if(to.type == "checkbox"){
						to.checked = this.checked;
					}else{
						to.value = this.value;
					}
				}
			}
		}	
	});		
}
function funReset_cvf(){ 
	$("#cvfModal :input").each(function(){
		if(this.type == "checkbox"){
			this.checked = false;		
		}else if(this.type == "textarea"){
			this.value = "";
		}else if(this.type == "radio"){
			this.checked = false;
		}else if(this.type == "text"){
			this.value = "";
		}
	});
	getClear('app_cvf_os_drawing');
	getClear('app_cvf_od_drawing');	
}

function funSave_cvf(){
	var strsave=$("#frmCvf").serialize(); 
	strsave+="&elem_saveForm=save_cvf";
	strsave+="&savedby=ajax";	
	$.post("saveCharts.php", strsave, function(data) {
			$("#cvfModal .close").trigger('click');
			loadExamsSummary("cvf");
		},'json');	
}
//CVF --------