// Toric JavaScript Document
/*****innerDim() TO GET INNER DIMENTIONS OF OPENED WINDOW/POPUP******/
function innerDim(){
	var arDim = new Array();
	arDim['h'] = $(window).innerHeight()-4;
	arDim['w'] = $(window).innerWidth();
	return arDim;
}

/*******SelectImg(param) TO MARK IMAGE SELECTED ON CLICK EVENT*********/
function SelectImg(t){
	$this 	= $(t);
	p		= $this.parent('div.image_container');
	s		= t.src;
	if(p.hasClass('selected_image_container')){
		p.removeClass('selected_image_container');
		delete selected_images[s];
	}else{
		p.addClass('selected_image_container');
		selected_images[selected_images.length] = s;
		flag_images_selected = true;
	}

	//TEMPORARY DEBUGGING.
	/*var temp = '';
	for(x in selected_images){
		temp += selected_images[x]+"<br>";
	}
	$('#debugging').html(temp);*/
}

/*******validForm() TO CHECK FOR VALID FORM, BEFORE SENDING SELECTING IMAGE TO ASSIGN TO CURRENT PATIENT.*****/
function validForm(){
	if(typeof(selected_images)!='undefined' && flag_images_selected){
		str = escape(JSON.stringify(selected_images));
		$('#hidd_selected_imgs').val(str);
		delete str;
	}else{
		top.fAlert('No image selected.');
		return false;
	}
	
}

/******openToric() TO OPEN TORIC CALCULATOR; SET THE PARENT POPUP TO REFRESH WHEN TORIC CLOSED********/
function openToric(){
	ReloadIfOtherPopClosed('Toric_Calc_pop');
	arDim = innerDim();
	window.opener.top.popup_win('toric.php','Toric_Calc_pop','location=0,status=1,resizable=1,left=1,top=1,scrollbars=1,width=1200,height='+arDim['h']);
}

function logOpenToric(){
	//if(toric_open_logged) return;
	$.ajax({
		url: "ajax_handler.php?task=log_toric_open",
		success: function(r){
			temp_str = r.split('_');
			if(temp_str.length==2){
				r = temp_str[0];
				open_log_id = temp_str[1];
				if(open_log_id!='' && (open_log_id==parseInt(open_log_id))){
					toric_open_log_id = open_log_id;
				}
			}
			//a=window.open(); a.document.write(r);
			if(r=='SAVED'){
				start_server_image_lookup();
			}
		}
	});	
}

/********ReloadIfOtherPopClosed(winName) TO REFRESH PARENT WINDOW, WHEN CHILD POPUP IS CLOSED*******/
function ReloadIfOtherPopClosed(n){
	setTimeout(function(){
		o = window.opener.top.tb_oPU[n];
		if(typeof(o)!='undefined'){
			t=setInterval(function(){
				if(o && o.closed == true){
					clearInterval(t);
					window.location.reload();
				}},500);
		}
	},1000);
}

function start_server_image_lookup(wait_time){
	if(typeof(wait_time)=='undefined') wait_time=2000;
	setTimeout(function(){
		$.ajax({
			url: "ajax_handler.php?task=show_recent_unlinked_img&sx_planning_sheet_id="+sx_planning_sheet_id,
			success: function(r){
				//alert(r);
				if(r=='EMPTY') {start_server_image_lookup();}
				else if(r.indexOf('IMAGE_ALLOCATED~~')>=0){
					var temp_arr 		= r.split('~~');
					var new_img_id 		= temp_arr[1];
					var thumb_image_url	= temp_arr[2];	
					//alert(new_img_id+' :: '+thumb_image_url);
					top.window.opener.set_toric_calc_image_data(new_img_id,thumb_image_url); //This function should be in SX_PLANNING_SHEET_popup.
					window.close();
				}
				else{
					img_json_string = r;
					r = JSON.parse(r);
					if(r != null && typeof(r)=='object'){
						$('#div_images').html('');
						for(x in r){
							img = r[x];
							$('#div_images').append('<div class="div_thumb"><a href="'+img+'" class="jpg_colorbox"><img src="'+img+'" style="max-width:140px; max-height:250px;"></a><div class="subheading alignLeft"><span class="closeBtn fr" onClick="del_unassigned_image(this,\''+img+'\')"></span><input type="radio" value="'+img+'" name="toric_assign_img" onClick="document.getElementById(\'hidd_free_images\').value=this.value"></div>');
						}
						$('#div_images_footer').html('<div class="mt5 text-center"><input type="hidden" name="hidd_free_images" id="hidd_free_images" value=""><input type="button" class="btn btn-success" value="Choose &amp; Done" onClick="AssignImage2Pt();"></div>');
						
						//$(".jpg_colorbox").colorbox({rel:'jpg_colorbox',iframe:false, width:"auto", height:"90%"});
						arDim = innerDim();
						$('#div_images').css({'max-height':(arDim['h']-220)+'px','overflow-x':'hidden','overflow-y':'auto'});
					}
				}
			}
		});
	},wait_time);
}

function del_unassigned_image(o,path){
	$(o).parent().parent().fadeOut('slow',function(){
		$.ajax({
			url: "ajax_handler.php?task=del_unassigned_image&path="+encodeURIComponent(path),
			success: function(r){
				//fAlert(r);
				start_server_image_lookup(0);
			}
		});		
	});
	
}

function AssignImage2Pt(path){
	if(typeof(path)=='undefined' || path=='') path = document.getElementById('hidd_free_images').value;
	if(typeof(path)=='undefined' || path==''){fAlert('No image selected.'); return;}
	$.ajax({
			url: "ajax_handler.php?task=AssignImage2Pt&path="+encodeURIComponent(path)+"&open_log_id="+toric_open_log_id,
			success: function(r){
				//alert(r);
				if(r.indexOf('assigned~~')>=0){
					var temp_arr 		= r.split('~~');
					top.window.opener.set_toric_calc_image_data(toric_open_log_id,temp_arr[1]); 
					
					window.close();
				}
				else alert(r);
				//start_server_image_lookup(0);
			}
	});
}

function capture_screen(img)
{
	$('#divLoader').css('display','block');
	//show loader
	$.ajax({
			url: "imw_toric_cal.php?upload=true&img="+img,
			success: function(r){
				//alert(r);
				//if(r=='assigned') window.close();
				//else alert(r);
				start_server_image_lookup(0);
			}
	});
}