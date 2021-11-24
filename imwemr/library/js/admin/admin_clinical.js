
function load_exam(obj){
	var exam = encodeURIComponent(obj.value);	
	var prm = "?task=load_exam&nm="+exam;	
	$.get(zPath+"/admin/chart_notes/chart_clinical_ajax.php"+prm, function(d){			
			
			$("#result_set").html(d.htm);
			$("#el_obsrv").html(d.obsrv);
		
		},'json');
}

function clearform(){
	$('#myModal textarea, #myModal input[id*=othr]').val('');
	$('#myModal :checkbox').prop('checked',false);
	$("#el_obsrv_name").val('');
}

function addNew(ed){
	var a = $("#el_exm_name").val();
	if(a!=""){	
		$("#el_exam").val(a);
		a = a.replace("--"," - ");		
		var modal_title="Exam Extension: "+a;
		$('#myModal .modal-header .modal-title').text(modal_title);
		if(typeof(ed)=="undefined"){ clearform(); $("#task").val("save"); $("#el_edid").val(""); }else{ $("#task").val("edit"); }	
		$('#myModal').modal('show');
	}else{		
		top.fAlert('Please select Exam.');	
	}
}

function editFormData(obj, edid){
	var a = $("#el_exm_name").val();
	var t = $(obj).html();
	var prm = "task=get_findings&edid="+edid+"&exm="+encodeURIComponent(a);	
	$.get(zPath+"/admin/chart_notes/chart_clinical_ajax.php",prm, function(d){			
			clearform();		
			//console.log(d);
			$("#el_obsrv_name").val(''+d.obsrv);
			$("#el_obsrv option[value='"+d.parent_obsrv+"']").prop("selected", true);
			$("#el_edid").val(edid);
			
			for(var x in d.finding){
				var z = d.finding[x];
				var y = $("#myModal input[type=checkbox][value='"+z+"']");
				if(y.length>0){y.prop("checked", true);}
				else{
					var p = d.finding_type[x];
					$("#myModal input[type=text][id*=el_"+p+"_othr]").each(function(){ if($(this).val()==""){ $(this).val(z).triggerHandler("blur"); return false; } });	
				}
			}
			addNew(1);
		},'json');
	
}

function saveFormData(delid, resetop){
	var m = "", strdel="";
	if(typeof(delid)=="undefined"||delid==""){
		var t = $("#el_obsrv_name").val(); t=$.trim(t);
		var c = t.match(/[a-zA-Z0-9]+/); c=$.trim(c);
		if(c==""){t="";}
		if(t==""){ m+="<br/>- Observation name (It should have alphabets in it.)"; }	
		var t = $("#add_edit_frm :checked[type=checkbox]").length;
		var s=0;
		$("#add_edit_frm input[type=text][id*=_othr]").each(function(){ var x = $(this).val(); x=$.trim(x); if(x!=""){ s=1; }});	
		if(s==0&&t==0){m+="<br/>- Grade/Location/Comments";}
	}else{
		var a = $("#el_exm_name").val();
		$("#el_exam").val(a);
		if(a!=""){
			if(typeof(resetop)!="undefined" && resetop!=""){
				strdel= "&reset=1"; delid="";	
			}else{	
				strdel= "&delid="+delid;	
			}
		}else{
			top.fAlert('Please select Exam.');
			return;
		}
	}
	
	if(m==""){
		//alert("SUBMIT");		
		var prm = $("#add_edit_frm").serialize();
		prm += strdel;		
		$.post(zPath+"/admin/chart_notes/chart_clinical_ajax.php",prm, function(d){			
			if(d==0){
				$('#myModal').modal('hide');
				$("#el_exm_name").trigger("change");
			}		
		},'text');

		
	}else{
		top.fAlert('Please fill in following:- '+m);		
	}
}

function ad_other_text(obj){
	var y = $(obj).val(); y = $.trim(y);
	if(typeof(y)!="undefined" && y!=""){
		
		var x = ["ABSENT", "PRESENT", "T", "1+", "2+", "3+", "4+", "SUPEROTEMPORAL", "INFEROTEMPORAL", "SUPERONASAL", "INFERONASAL", "COMMENTS"];
		var yu = y.toUpperCase();
		
		if(x.indexOf(yu)==-1){		
		var f=0; p=0;	
		$(obj).parent().parent().find("input[type=text]").each(function(){ if($(this).val()==""){f=1;} var id = $(this).attr("id"); id = id.replace(/el_grd_othr|el_loc_othr/,""); if(parseInt(p)<parseInt(id)){p=id;}  });	
		if(f==0){
			p=parseInt(p)+1;			
			id = $(obj).prop("id");
			var idn = id.replace(/\d+/,p);
			var x=""+
			"<div class=\"input-group\">"+
					"<input id=\""+idn+"\" type=\"text\" class=\"form-control\" name=\""+idn+"\" placeholder=\"Other\">"+
					"<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-remove\"></i></span>"+   
			"</div>";					
			$(obj).parent().parent().append(x);
			$("#"+idn).bind("blur",function(){ad_other_text(this);});
			$("#"+idn).parent().find(".glyphicon").bind("click", function(){del_other_text(this);});
		}
		}else{ $(obj).val(''); }
	}
}
function del_other_text(obj){	
	$(obj).parent().parent().find(".form-control").val("");
}

var ar = [["add_new","Add New","top.fmain.addNew();"], ["btn_reset","Reset Exam","top.fmain.saveFormData(1, 1);"]];

$(document).ready(function(){
	set_header_title('Clinical Exam Extensions');
	top.btn_show("ADMN",ar);
	
	$("#col_opts input[type=text][id*=_othr]").bind("blur", function(){ad_other_text(this);});
	$("#col_opts .input-group .glyphicon").bind("click", function(){del_other_text(this);});
	
	show_loading_image('none');
	
});