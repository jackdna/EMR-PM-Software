

$(function(){
	
	$(".selectpicker_new").each(function(i,elem){
		var id = $(elem).attr('id');
		var val = $(elem).val();
		if(val == 'Other')
		{
			$('#div_'+id).addClass('hidden');
			$('#other_'+id).removeClass('hidden');
		}
		
	});
	
	$("#elem_chronicDesc_other").on('keyup',function(){
		textAreaAdjust($("#elem_chronicDesc_other")[0]);	
	});
	
	textAreaAdjust($("#elem_chronicDesc_other")[0]);
	
});

function textAreaAdjust(o) {
	o.style.minHeight = 0 ;
  o.style.minHeight = (25+o.scrollHeight)+"px";
}