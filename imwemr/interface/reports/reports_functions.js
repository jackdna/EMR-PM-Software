
function DateOptions(val){

	switch(val){
		case 'Date':
		{
			$('#dateFieldControler').hide();
			$('#dateFields').show();
			$('#monthly_vals').hide();
			break;
		}
		case 'Monthly':
		{
			if(dgi('monthly_vals')){
				$('#dateFieldControler').hide();
				$('#dateFields').hide();
				$('#monthly_vals').show();
			}
			break;
		}
		default:
		{
			$('#dateFields').hide();
			$('#monthly_vals').hide();
			$('#dateFieldControler').show();
			if(val=='x') $("#dayReport option[value='Daily']").attr('selected', 'selected');
			else if(val=='m') $("#dayReport option[value='Daily']").attr('selected', 'selected');
			break;
		}
	}
}

function popup_dbl(divid,sourceid,destinationid,act,odiv){
	alert('fdfdsf');
	if(act=="single" || act=="all"){
		if(act=='single')	{
			$("#"+sourceid+" option:selected").appendTo("#"+destinationid);
		}else if(act=="all"){$("#"+sourceid+" option").appendTo("#"+destinationid);}
	}else if(act=="single_remove" || act=="all_remove"){
		if(act=="single_remove"){$("#"+sourceid+"  option:selected").appendTo("#"+destinationid);}
		if(act=="all_remove")	{$("#"+sourceid+"  option").appendTo("#"+destinationid);}
		$("#"+destinationid).append($("#"+destinationid+" option").remove().sort(function(a, b) {
			var at = $(a).text(), bt = $(b).text();
			return (at > bt)?1:((at < bt)?-1:0);
		}));
		$("#"+destinationid).val('');
	}else{
		$("#"+destinationid+" option").remove();
		$("#"+odiv+" option").clone().appendTo("#"+destinationid);
		$("#"+divid).show("clip");
	}
}
function selected_ele_close(divid,sourceid,destinationid,div_cover,action, callFrom){
	if(action=="done"){
		var sel_cnt=$("#"+sourceid+" option").length;
		if(callFrom==''){ 
			$("#"+divid).hide("clip"); 
		}
		$("#"+destinationid+" option").each(function(){$(this).remove();})
		$("#"+sourceid+" option").appendTo("#"+destinationid);
		$("#"+destinationid+" option").attr({"selected":"selected"});
		$("#"+div_cover).width(parseInt($("#"+destinationid).width())+'px');
		if(sel_cnt>8){
			$("#"+div_cover).width(parseInt($("#"+destinationid).width()-15)+"px");	
		}
	}else if(action=="close"){
		$("#"+divid).hide("clip");
	}
}

function getCSVData(){
	if($("#csv_text").val()==''){
	var csv_value=$('#csvFileDataTable').table2CSV({delivery:'value'});
	$("#csv_text").val(csv_value);
	}
}