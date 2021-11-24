function alert_msg(action,count,msg)
{//alert(action);
	if(action==='update' || action==='success')
	{
		msg = msg || 'Record(s) Saved Successfully';
		if(top.document.getElementById('alert_success')) {
			if(msg) {
				top.$('#alert_success').html(msg);	
			}
			top.$('#alert_success').show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );	
		}else {
			if(msg) {
				$('#alert_success').html(msg);	
			}
			$('#alert_success').show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );
		}
		//.css({left:"-1000px",display:'block'});
	}else if(action==='finalize')
	{
		if(top.document.getElementById('alert_finalize')) {
			top.$('#alert_finalize').show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );
		}else {
			//$('#alert_finalize').show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );;
		}
	}
	else if(action==='del')
	{
		if(count===1)
		{
			$("#alert_del").text(count+" Record Deleted");
		}
		else
		{
			$("#alert_del").text(count+" Records Deleted");
		}
		if(top.document.getElementById('alert_success')) {
			top.$('#alert_delete').show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );
		}else {
			$('#alert_delete').show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );
		}
	}
	else if(action==='error')
	{
		// Here count param used to display error msg
		if(top.document.getElementById('alert_error')) {
			top.$('#alert_error').text(count).show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );
		}else {
			$('#alert_error').text(count).show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );
		}
	}
	else
	{	
		if(top.document.getElementById('alert_success')) {
			top.$('#alert_error').show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );	
		}else {
			$('#alert_error').show(1).animate({left:'75px'},1000).delay(2000).fadeOut(300,function(){ $(this).css({left:'-1000px'});} );
		}
	}
	
}