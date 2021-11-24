
//----------

function saveFormData(){
	top.show_loading_image('hide');
	var data = $("#test_diagnosis").serialize();
	$.ajax({
		url:'test_diagnosis.php',
		data: data,
		method: 'POST',
		beforeSend: function(obj){
			top.show_loading_image('show','300', 'Saving Records...');
		},
		success: function(resp){				
			top.alert_notification_show("Test Diagnosis Saved Successfully.");
			top.show_loading_image('hide');
			$("#opdel").val("");
			if($("#dv_test_sb_typ").hasClass("hidden")){$("#el_test_nm").trigger("change");}else{ $("#el_test_sb_typ").trigger("change");}
		}
	});
}

function load_diag(co){
	var o = $("#el_test_nm")[0];
	var p = $("#el_test_sb_typ")[0];
	if(o && o.value!=""){
		
		if(co.id == o.id){ $("#dv_test_sb_typ").addClass("hidden"); p.value=""; $("#tests_data").html("");}
		
		var ar = o.value.split('-@-');
		var utst = $.trim(ar[1]); 
		
		//console.log(""+utst);
		
		if(utst == "14" || utst == "15" || utst == "9" || utst == "16"){ //9:OCT, 14:Fundus, 15:External/Anterior
			if(p.value==""){				
				if($("#dv_test_sb_typ").hasClass("hidden")){
					$("#el_test_sb_typ").html("<option value=''></option>");
					var str_html="";
					if(utst == "14"){str_html="<option value='Disc Photos'>Disc Photos</option><option value='Macula Photos'>Macula Photos</option><option value='Retina Photos'>Retina Photos</option>";}
					else if(utst == "15"){str_html="<option value='ES (External)'>ES (External)</option><option value='ASP (Anterior Segment Photos)'>ASP (Anterior Segment Photos)</option>";}
					else if(utst == "9"){str_html="<option value='Optic Nerve'>Optic Nerve</option><option value='Retina'>Retina</option><option value='Anterior Segment'>Anterior Segment</option>";}	
					else if(utst == "16"){str_html="<option value='Topography'>Topography</option><option value='Treatment'>Treatment</option>";}
					$("#el_test_sb_typ").html("<option value=''></option>"+str_html);
					$("#dv_test_sb_typ").removeClass("hidden");
				}
				return;	
			}			
		}else{ $("#dv_test_sb_typ").addClass("hidden"); p.value=""; }
		
	var prm = "op=getform&tstnm="+utst+"&tst_sb_typ="+encodeURI(p.value);		
	$.get('test_diagnosis.php',prm,function(data){
			if(data!=""){$("#tests_data").html(data);}
			
		});
	}else{  }	
}

function switch_allVals(obj){
	if($(obj).is(':checked')) {
		$(".test_chkbx").prop("checked",true);
	}
	else{
		$(".test_chkbx").prop("checked",false);
	}
}

function deleteop(){
	if($(":checked.test_chkbx").length>0){	$("#opdel").val(1); saveFormData();  }else{ alert("Please select diagnosis."); }	
}

function footer_btn_ttemplates(flag){
	if (typeof(flag)=='undefined') flag = 'show';
	if(flag=='show'){
		var ar = [
			 
			  ["saveTestDiagnosisTab","Save","top.fmain.saveFormData();"],
  			  ["saveTestDiagnosisTab","Delete","top.fmain.deleteop();"],
			  
			 ];
		top.btn_show("ADMN",ar);	
	}
}
$(document).ready(function(){	
	set_header_title('Test Diagnosis');
	footer_btn_ttemplates();	
});