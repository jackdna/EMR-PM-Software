
function load_hpi(){
	$.get("chart_hpi_ext_ajax.php?task=hpi",function(d){$("#result_set").html(d);});
}

function load_sub(o){
	var v=o.value; $("#el_subcat_name").val("");
	if(v!=""){	$.get("chart_hpi_ext_ajax.php?task=subcats&lvl="+v,function(d){$("#el_subcat_name").html(d);}); }
}

function ad_other_text(){	
	var ad=1, c=0;
	$("#add_edit_frm :input[type=text][id*=el_hpi]").each(function(){ var t = this.id.replace(/el_hpi/, ''); if(t>c){c=t;};  if($(this).val()==""){ad=0;}; });
	if(ad==1){
		c = parseInt(c);
		c+=1;
		var c1=c+1;
		var ht="<div class=\"row\">"+
					"<div class=\"col-sm-6\" >"+
						"<div class=\"input-group\">"+
						   " <input type=\"text\" class=\"form-control\" name=\"el_hpi"+c+"\" id=\"el_hpi"+c+"\" placeholder=\"HPI\">"+
						    "<input type=\"hidden\" name=\"el_edid"+c+"\" id=\"el_edid"+c+"\" value=\"\" >	"+
						    "<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-remove\"></i></span>" 	+					    
						"</div>"+
					"</div>"+
					"<div class=\"col-sm-6\" >"+
						"<div class=\"input-group\">"+
						    "<input type=\"text\" class=\"form-control\" name=\"el_hpi"+c1+"\" id=\"el_hpi"+c1+"\" placeholder=\"HPI\">"+
						    "<input type=\"hidden\" name=\"el_edid"+c1+"\" id=\"el_edid"+c1+"\" value=\"\" >"+
						    "<span class=\"input-group-addon\"><i class=\"glyphicon glyphicon-remove\"></i></span>" 	+					    
						"</div>"+
					"</div>"+
				"</div>";
		$(".modal-body").append(ht);
		$("#add_edit_frm input[type=text][id*=el_hpi]").bind("blur", function(){ad_other_text();});
	}
}

function del_other_text(obj){	
	$(obj).parent().parent().find(".form-control").val("");
}

function addNew(){	
	$('#myModal').modal('show');
}
function saveFormData(op){
	
	var m="", strdel="";
	if(typeof(op)!="undefined"&&op=="del"){		
		var did="";
		$(":checked[id*=el_hpi_id]").each(function(){ if(this.value!=""){did+=this.value+", ";} });
		if(did==""){top.fAlert('Please select records to delete.'); return;}	
		if(confirm("Are you sure to delete?")){	strdel="&op="+op+"&did="+encodeURIComponent(did); }
	}else{
		if($("#el_cat_name").val()==""){ m+="<br/>&nbsp;- Category"; }
		if($("#el_subcat_name").val()==""){ m+="<br/>&nbsp;- Sub Category"; }
		var c=0;
		$("input[id*=el_hpi]").each(function(){ if($(this).val()!=""){ c=1; return false; } });
		if(c==0){ m+="<br/>&nbsp;- HPI"; }
		if(m!=""){ top.fAlert('Please enter following:-'+m); return; }
	}
	
	//
	var prm = $("#add_edit_frm").serialize();
	prm += strdel;
	$.post("chart_hpi_ext_ajax.php",prm, function(d){		
		if(d=="0"){			
			$('#myModal').modal('hide');
			load_hpi();
			$("#add_edit_frm :input[type=text]").each(function(){ $(this).val(""); });	
		}		
	},'text');	
}

function edit(o){
	var eid = $(o).parents("tr").find("input[id*=el_hpi_id]").val();
	$.get("chart_hpi_ext_ajax.php?task=get_edit_val&id="+eid, function(d){
			if(d){
				$("#el_subcat_name").html(d.opt);
				$("#el_subcat_name option[value='"+d.sub_cat+"']").prop("selected", true);
				$("#el_cat_name option[value='"+d.cat+"']").prop("selected", true);
				$("#el_hpi1").val(""+d.hpi);
				$("#el_edid1").val(""+d.id);
				addNew();	
			}		
		},'json');
}

var ar = [["add_new","Add New","top.fmain.addNew();"],		
		["del","Delete","top.fmain.saveFormData('del');"],
		];

$(document).ready(function(){
	set_header_title('Custom HPI');
	load_hpi();
	top.btn_show("ADMN",ar);
	
	$("#add_edit_frm input[type=text][id*=el_hpi]").bind("blur", function(){ad_other_text();});
	$("#add_edit_frm .input-group .glyphicon").bind("click", function(){del_other_text(this);});
	
	show_loading_image('none');
	
});