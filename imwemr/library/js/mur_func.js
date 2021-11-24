	function loadGenHealth(){
		//Skipped as the section is not developed 
		$('#genHealthDiv_wv').load('../chart_notes/requestHandler.php?elem_formAction=show_gen_health',function(){$('#genHealthDiv_wv').modal('show');});
	}
	
	function ptInfoReviewed()
	{
		if( window.opener )
		{
			if( window.opener.top.fmain.ptInfoReviewed )
				window.opener.top.fmain.ptInfoReviewed();
		}
	}
	
	function openMedHX(op, winH)
	{
		if( window.opener )
		{
			if( window.opener.top.fmain.openMedHX )
				window.opener.top.fmain.openMedHX(op, winH);
		}
	}
	
	function genTempKey(tempKeySize,pid,regen_key) {
		$.ajax({
			url:window.opener.JS_WEB_ROOT_PATH+'/interface/MU_checklist/ajax_handler.php',
			data:'temp_key_size=6&pid='+pid+'&regen_key=0&user_pass=DoNtChEcK',
			type:'POST',
			success:function(respData){
				resultData = respData;
				$('#temp_key').val(resultData);
			}
		});		
	}
	
	function saveFun(t){
		var dataVal = '';
		switch(t.id){
			case 'idunlockPatient':
				dataVal = 'do=idunlockPatient';
				break;
			case 'tempKeyGiven':
				break;
			case 'vsSave':
				if($('#bps').val()=='' || $('#bpd').val()=='' || $('#hght').val()=='' || $('#wght').val()==''){
					fAlert('All the fields of Vital Information are compulsary to fill with data.');
					return false;
				}
				dataVal = 'do=vsSave&bps='+$('#bps').val()+'&bpd='+$('#bpd').val()+'&hght='+$('#hght').val()+'&hght_unit='+$('#hght_unit').val()+'&wght='+$('#wght').val()+'&wght_unit='+$('#wght_unit').val()+'&bmi_val='+$('#bmi_val').val();
				
				break;
			case 'smokingStatus':
				var SmokingStatus = $("#SmokingStatus").val();
				var source_of_smoke = $("#source_of_smoke").val();
				var source_of_smoke_other = $("#source_of_smoke_other").val();
				var smoke_perday = $("#smoke_perday").val();
				var number_of_years_with_smoke = $("#number_of_years_with_smoke").val();
				var smoke_years_months = $("#smoke_years_months").val();
				
				if($("#offered_cessation_counseling").is(':checked')){var offered_cessation_counseling = 1;}
				else{var offered_cessation_counseling = '';}
				
				var txtDateOfferedCessationCounselling = $("#txtDateOfferedCessationCounselling").val();
				var cessationCounselling = $("#cessationCounselling").val();
				var cessationCounsellingOther = $("#cessationCounsellingOther").val();
				
				dataVal = 'do=smokingStatus&SmokingStatus='+SmokingStatus+'&source_of_smoke='+source_of_smoke+'&source_of_smoke_other='+source_of_smoke_other+'&smoke_perday='+smoke_perday+'&number_of_years_with_smoke='+number_of_years_with_smoke+'&smoke_years_months='+smoke_years_months+'&offered_cessation_counseling='+offered_cessation_counseling+'&txtDateOfferedCessationCounselling='+txtDateOfferedCessationCounselling+'&cessationCounselling='+cessationCounselling+'&cessationCounsellingOther='+cessationCounsellingOther;
				break;
		}
		$.ajax({
			url:window.opener.JS_WEB_ROOT_PATH+'/interface/MU_checklist/ajax_handler.php?save_mu_data=yes',
			data:dataVal,
			type:'POST',
			success:function(rd){
				r = rd;
				if(r=='')	window.location.reload();
				else 		fAlert(r);
			}
		});
		
	}
	
	$(document).ready(function(e) {
		window.resizeTo(1000,800);
		
		$('.adminbox').on('click','span',function(){
			var parent_obj = $(this).parent().parent();
			$(parent_obj).children('li').each(function(id,elem){
				if($(elem).children('div').length > 0){
					if($(elem).children('div').hasClass('in') === true){	
						$(elem).children('div').removeClass('in');
					}
				}
			});
		});
		
		
		//Skipped ERX
		$.ajax({
			url:'downerx.php',
			type:'GET',
			success:function(rd){
				r = rd;
				if(r != 'Electronic Prescriptions found <b>0</b> for today.' && r.indexOf('found')>0){$('#erx_tick').removeClass('text-info').addClass('text-green');}
				$('#erx_box').html(r);
			}
		}); 
	});