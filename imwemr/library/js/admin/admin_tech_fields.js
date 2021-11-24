var ar = [["tech_field_save","Save","top.fmain.submit_frm();"]];
top.btn_show("ADMN",ar);
function sel_check(val){
	if(val=='yes'){
		document.getElementById('distanceyes').checked=true;
		document.getElementById('nearyes').checked=true;
		document.getElementById('aryes').checked=true;
		document.getElementById('pcyes').checked=true;
		document.getElementById('mryes').checked=true;
		document.getElementById('distanceno').checked=false;
		document.getElementById('nearno').checked=false;
		document.getElementById('arno').checked=false;
		document.getElementById('pcno').checked=false;
		document.getElementById('mrno').checked=false;
	}else if(val=='no'){
		document.getElementById('distanceyes').checked=false;
		document.getElementById('nearyes').checked=false;
		document.getElementById('aryes').checked=false;
		document.getElementById('pcyes').checked=false;
		document.getElementById('mryes').checked=false;
		document.getElementById('distanceno').checked=true;
		document.getElementById('nearno').checked=true;
		document.getElementById('arno').checked=true;
		document.getElementById('pcno').checked=true;
		document.getElementById('mrno').checked=true;
	}

}

function chk4Other(str){
	top.show_loading_image('block');
	var o=document.getElementById(str)
	var oo = document.getElementById(str+"Other");
	if((o!=null) && ( oo != null ) ){ 
		if(o.value == "Other" ){
			oo.style.visibility = "visible";			
		}else{			
			oo.style.visibility = "hidden";
			oo.value = "";
			document.frmVisit.elem_visit.value = o.value;
			document.frmVisit.submit();
		}
	}
}

function setCheckAction(obj1,obj2){
	if($("#"+obj1)){
		$("#"+obj1).attr("checked",true);
	}
	if($("#"+obj2)){
		$("#"+obj2).attr("checked",false);
	}
}

function submit_frm(){
	document.tech.save_data.value = 'save';
	document.tech.submit();
	
}
set_header_title('Tech Fields');
show_loading_image('none');