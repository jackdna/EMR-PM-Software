function addnew(){
	var x = $("#elem_cntr").val();
	var str ="<tr> "+
		"<td >"+
		"<input type=\"text\" class=\"form-control\" name=\"elem_drawicon_name[]\" value=\"\">"+
		"</td>"+
		"<td style=\"position:relative;\"  align=\"left\" >"+
		"<input type=\"file\" name=\"elem_drawicon_file"+x+"\" value=\"\" style=\"margin-left:60px;\" >"+
		"</td>"+
		"<td>"+
		"<input type=\"text\" class=\"form-control\" id=\"elem_drawicon_symptom"+x+"\" name=\"elem_drawicon_symptom[]\" value=\"\">"+
		"</td>"+
		"<td style=\"width:20px; padding-left:20px;\" class=\"text-center\">"+
		"<div class=\"checkbox\"><input type=\"checkbox\" name=\"elem_drawicon_del[]\" id=\"elem_drawicon_del[]"+x+"\" value=\"\"><label for=\"elem_drawicon_del[]"+x+"\">&nbsp;</label></div>"+
		"<input type=\"hidden\" name=\"elem_drawicon_edid[]\" value=\"\">"+
		"</td>"+
		"</tr>";
	$("#tblupload").append(str);	
		$(":input[name*=elem_drawicon_symptom]").each(function(id, elem){
		$(elem).typeahead({source:arrTHSym});
	});	
	x=parseInt(x)+1;
	$("#elem_cntr").val(x);
}

function checkFields(op){
	$("#elem_edit_drawicon").val(op);	
	document.frm_drawicon.submit();		
}

function setfiletag(o){
	var id = o.id;
	var i = parseInt(id.replace('elem_drawicon_edit',''));
	$(o).replaceWith("<input type=\"file\" id=\""+id+"\" name=\"elem_drawicon_file"+i+"\" value=\"\" style=\"margin-left:60px;\" >");
	$("#"+id).trigger("click");
}
	
$(document).ready(function(){			
	$(':input[name*=elem_drawicon_symptom]').each(function(id, elem){
		$(elem).typeahead({source:arrTHSym});
	});	
});

var ar = [["addDrawiconTab","Add New","top.fmain.addnew();"],["saveDrawiconTab","Save","top.fmain.checkFields(1);"],["deleteDrawiconTab","Delete","top.fmain.checkFields(2);"]];
top.btn_show("ADMN",ar);
set_header_title('Drawings');
top.show_loading_image('none');