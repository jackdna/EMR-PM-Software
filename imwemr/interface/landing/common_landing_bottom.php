    
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.min.1.12.4.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/bootstrap.min.js"></script>
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/jquery.mCustomScrollbar.concat.min.js"></script>
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/imw_scroll.js"></script>
<script src="<?php echo $GLOBALS['webroot'];?>/library/js/owl.carousel.min.js"></script> 
<script>
(function($){
	$(window).load(function(){
		
		$("#content-1").mCustomScrollbar({
			theme:"minimal"
		});
		
	});
})(jQuery);

$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

$(document).ready(function(){
	$('.owl-carousel').owlCarousel({
		loop:true,
		margin:10,
		navText:false,
		nav:true,
		dots:false,
		responsive:{
			0:{
				items:1
			},
			630:{
				items:1
			},
			1000:{
				items:1
			},
			
		}
	});
	
});


function deleteMessages(section)
{
	/*Checked Message*/
	var selMsg = $('.landing_'+section+'_checked:checked').map(function(){return $(this).val();}).get();
	
	var directName = (section === 'direct')? section+' ' : '';
	
	if( selMsg.length <= 0 )
	{
		top.fAlert('Please Select '+directName+'message to delete.');
		return true;
	}
	
	top.fancyConfirm('Are you sure want to delete the selected '+directName+'message(s)?', 'top.fmain.delMsgAction(\''+section+'\')');
}


function delMsgAction(section ){
	
	var selMsg = $('.landing_'+section+'_checked:checked').map(function(){return $(this).val();}).get();
	
	$.ajax({
		url: top.JS_WEB_ROOT_PATH+'/interface/physician_console/ajax_html.php',
		data: 'from=console&task=del_'+section+'&msg_id='+selMsg+'&del_type=0',
		type:'POST',
		success:function()
		{
			//top.show_loading_image('show', '', 'Message(s) Deleted Successfully. Loading Data. Please hold a moment...');
			reload('load_'+section);
		}/*,
		complete: function(){
			top.show_loading_image('hide', '', 'Processing. Please hold a moment...');
		}*/
	});	
}

var landingSectionLoader = '<div id="div_loading_image" class="text-center" style="display: block;position: initial;padding-top: 50px;height: 100%;width: 100%;background-color: rgba(0,0,0,0.1);"><div class="loading_container" style="width: auto;"><div class="process_loader"></div><div id="div_loading_text" class="text-info">Loading Data....</div></div></div>';

function reload( action )
{
	$.ajax({
		url: top.JS_WEB_ROOT_PATH+'/interface/landing/ajax.php',
		type:'POST',
		data:'task='+action,
		beforeSend: function(){
			$('#'+action).mCustomScrollbar('updateContent', landingSectionLoader, true);
		},
		success:function(data)
		{
			if(action=='appointmentSummary')
			{
				var arr=data.split('~:~');
				$('#'+action).mCustomScrollbar('updateContent', arr[1]);
				//$('#'+action).html(arr[1]);
				if(arr[0]){
					pie_chart('pie','appt_summary_div',arr[0],'','','2');
				}
			}
			else
			{
				$('#'+action).mCustomScrollbar('updateContent', data);
			}
		}
	});	
}
	//functio to delete to do note
function delete_patient_note(action, id, cnfrm){	 // mode is 'list'; pageMode is 'all/sel'
			
	if(typeof(cnfrm)=="undefined"){
		top.fancyConfirm('Are you sure to delete this note?', '', 'window.top.fmain.delete_patient_note("'+action+'", "'+id+'", true)');
		return;
	}
	else if(cnfrm==true){
		var load_dt = $("#load_dt").val();
		$.ajax({ 
			url: "../scheduler/physician_scheduler/patient_notes.php?ajax_request=yes&id="+id+"&action="+action,
			success: function(resp){
				//cal reload page
				reload('todoList');
			}
		});
	}else {
		return false;
	}
}
</script> 