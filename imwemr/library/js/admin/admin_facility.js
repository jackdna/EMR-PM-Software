	var arrAllShownRecords = facilities = new Array();
	var totalRecords	   = 0;
	var hq_exists		   = 0;
	var fac_types		   = '';
	var fac_types2		   = '';	
	var REMOTE_SCH		   = 0;
	var	msg				   = ""; 
	var formObjects		   = new Array('id','facility_type','fac_prac_code','name','pam_code','server_name','out_ofoffice',
	'street','postal_code','city','state','phone','fax','ele_txt_1','apply_providers','default_group','facility_npi','mess_of_day',
	'facility_color','Confidential_psw','cbk_include_in_app_export','waiting_timer','chart_timer','chart_finalize',
	'regular_time_slot','maxRecentlyUsedPass','maxLoginAttempts','maxPassExpiresDays','billing_location','accepts_assignment',
	'billing_attention','ptinfodiv','mur_audit','EmdeonUrl','fd_pc','enable_hp');

	var scanUploadSrc = '';
	
	function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Loading Facilities...');

		if(typeof(s)!='string' || s==''){s = 'Active';}
		s_url = "&s="+s;
		
		if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
		if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
		
		oso		= $('#ord_by_field').val(); //old_so
		soAD	= $('#ord_by_ascdesc').val();
		if(typeof(so)=='undefined' || so==''){
			so 		= $('#ord_by_field').val();
		}else{
			$('#ord_by_field').val(so);
			if(oso==so){
				if(soAD=='ASC') soAD = 'DESC';
				else  soAD = 'ASC';
			}else{
				soAD = 'ASC';
			}
			$('#ord_by_ascdesc').val(soAD);
		};
		//so 		= 'pos_prac_code';
		$('.link_cursor span').removeAttr('class');
		if(soAD=='ASC')	$(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
		else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
		
		so_url='&so='+so+'&soAD='+soAD;
		ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url;

		$.ajax({
		  url: ajaxURL,
		  success: function(r) {
			showRecords(r);
		  }
		});
	}
	
	var hq_fac_id='';
	function showRecords(r){
		r = JSON.parse(r);
		result 		= r.records;
		pos_facilities 	= r.pos_facilities;
		emdeon_facilities = r.emdeon_facilities;
		hq_exists = r.hq_exists;
		REMOTE_SCH = r.REMOTE_SCH;
		if(REMOTE_SCH==1){
			if($('#trServers').hasClass('hide') === true){
				$('#trServers').removeClass('hide');
			}
			$('#server_name').html(r.optServers)
		}
		
		//CHART TIMER
		var days_list= new Array('.5','1','1.5','2','2.5','3','3.5','4','4.5','5');
		var opt_days='';
		for(s1 in days_list) {
			opt_days+='<option value='+days_list[s1]+'>'+days_list[s1]+'</option>';
	 	}
		$('#chart_timer').html(opt_days);
		//REGULAR TIME SLOT 
		var regular_list=new Array('5','10','15','20','30');
		var opt_time_slots='';
		for(s2 in regular_list) {
			opt_time_slots+='<option value='+regular_list[s2]+'>'+regular_list[s2]+'</option>';
		}
		$('#regular_time_slot').html(opt_time_slots);
		//CHART FINALIZE
		var opt_finalize='';
		for(s1=1;s1<=31;s1++) {
			opt_finalize+='<option value='+s1+'>'+s1+'</option>';
		}
		$('#chart_finalize').html(opt_finalize);
		
		// COPAY POLICIES
		if(r.copayPolicies['ptinfodiv']==1){
			$('#ptinfodiv').prop('checked', true);
		}
		if(r.copayPolicies['mur_audit']==1){
			$('#mur_audit').prop('checked', true);
		}
		if(r.copayPolicies['fd_pc']==1){
			$('#fd_pc').prop('checked', true);
		}
		if(r.copayPolicies['erx_entry']==1){
			$('#erx_entry_1').prop('checked', true);
		}else{
			$('#erx_entry_2').prop('checked', true);
		}
		if(r.copayPolicies['Allow_erx_medicare']=='Yes'){
			$('#Allow_erx_medicare_1').prop('checked', true);
		}else{
			$('#Allow_erx_medicare_2').prop('checked', true);
		}
		
		if(r.copayPolicies['EmdeonUrl']=='https://cli-cert.changehealthcare.com'){
			$('#EmdeonUrl_1').prop('checked', true);
		}else if(r.copayPolicies['EmdeonUrl']=='https://clinician.changehealthcare.com'){
			$('#EmdeonUrl_2').prop('checked', true);
		}
		
		h='';
		if(r != null){
			var row = '';
			row_class = '';
			for(x in result){
				s = result[x];
				rowData = new Array();
				row += '<tr class="link_cursor'+row_class+'">';
				var fac=0;
				for(y in s){
					tdVal = s[y];
					rowData[y] = tdVal;
					//alert(y+' => '+tdVal);

					if(y=='id'){pkId = tdVal; row += '<td style="width:1%"><div class="checkbox checkbox-inline"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}//alert(pkId+':'+y);
					if(y=='name'){
						row	+= '<td class="text-left" style="width:18%" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
					}
					if(y=='location'){
						row	+= '<td class="text-left" style="width:18%" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
					}
					if(y=='groupName'){
						row	+= '<td class="leftborder alignLeft" style="width:18%" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
					}
					if(y=='out_ofoffice'){
						row	+= '<td class="leftborder alignLeft" style="width:18%" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
					}
					if(y=='facility_phone'){
						row	+= '<td class="leftborder alignLeft" style="width:18%" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
					}
					if(y=='facility_type'){
						if(tdVal=='1') { hq_fac_id = pkId; }
						row+= '<td style="width:18%;display:none;" onclick="addNew(1,\''+pkId+'\');"><input type="hidden" size="1" value="'+tdVal+'" name="hq_val[]"></td>';
					}
				}
				if(row_class==''){row_class=' alt';}else{row_class='';}
				totalRecords++;
				row += '</tr>';
				arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
			}
			h = row;
		}

		$('#result_set').html('<input id="hq_exists" type="hidden" value="'+hq_exists+'" />'+ h);		
		top.show_loading_image('hide');
		
		// FAC TYPES
		//This check is made to make facility type value selected in selectpicker
		var facility_loc_val = '';
		var facility_hq_val = '';
		if(hq_exists == 2){
			facility_loc_val = 'selected';
		}else{
			facility_hq_val = 'selected';
		}
		
		
		fac_types = '<option value="">Select</option><option value="1" class="hq_hd">HQ</option><option value="2">Location</option>';
		$('#facility_type').html(fac_types);
		
		// POS FACILITIES
		fac_options = '';
		for(x in pos_facilities){
			ff = pos_facilities[x];
			fac_options += '<option value="'+ff.pos_facility_id+'">'+ff.facilityPracCode+'</option>';
		}			
		$('#fac_prac_code').html(fac_options);
		$('#erx_facility_id').html(emdeon_facilities);
		$("input[name='hq_val[]']").each(function()
		{				
			if($(this).val() == '1'){
				$('.checkbox', $(this).parent().parent()).css({'display':'none'});	
				$('.checkbox', $(this).parent().parent()).removeClass('chk_sel');
				$(this).parent().parent().addClass('bg-info');
			}
		});
		$('.selectpicker').selectpicker('refresh');
	}
	
	function addNew(ed,pkId){
		if(typeof(ed)!='undefined' && ed!=''){
			$('#addNew_div .modal-header .modal-title').text('Edit Record');
			if(hq_fac_id==pkId){
				if($('#divLogoLink').hasClass('hide') === true){
					$('#divLogoLink').removeClass('hide');
					$('#grpInfo_div1').css('min-height','170px');
				}
			}else{
				if($('#divLogoLink').hasClass('hide') === false){
					$('#divLogoLink').addClass('hide');
					$('#grpInfo_div1').css('min-height','228px');
				}
			}
			if($('#divSchProviders').hasClass('hide') === true){
				$('#divSchProviders').removeClass('hide');
			}
			
		}else {
			if($('#idone').hasClass('hide') === false){
				$('#idone').addClass('hide');
			} 
			$('#addNew_div .modal-header .modal-title').text('Add New Record'); 
			if($('#divLogoLink').hasClass('hide') === false){
				$('#divLogoLink').addClass('hide');
				$('#grpInfo_div1').css('min-height','228px');
			}
			
			if($('#divSchProviders').hasClass('hide') === true){
				$('#divSchProviders').removeClass('hide');
			}
			$('#id').val(''); document.add_edit_frm.reset();
			if( $("#hq_exists").val() == 1 ) { $('.hq_hd').addClass('hide'); }
			$('.selectpicker').selectpicker('refresh');
		}
		$('#addNew_div').modal('show');
		
		$('#addNew_div').on('shown.bs.modal', function(){
		//	$(".grid_color_picker11").spectrum({color:'#ffffff'});
			try{
				$('#facility_color').val('#FFFFFF');
				$('div.sp-preview-inner').css('background-color','#FFFFFF');
			}catch(e){
				alert(e.message);
			}
			
		});
		
		$('.selectpicker').selectpicker('refresh');
		set_modal_height('addNew_div');
		if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	}
	
	function upload_facility_logo(){
		top.popup_win(top.JS_WEB_ROOT_PATH+'/interface/admin/facility/upload_win.php');
	}

	function fillEditData(pkId){
		f = document.add_edit_frm;
		e = f.elements;
		$('#id').val(pkId);

		for(i=0;i<e.length;i++){
			o = e[i];
			if($.inArray(o.name,formObjects)){
				on	= o.name;
				if(on=='Confidential_psw1'){ on='Confidential_psw'; }					

				v	= arrAllShownRecords[pkId][on];
				if(o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
					if (o.type == "checkbox" || o.type == "radio") {
						oid = on+'_'+v;
						if(o.name=='apply_providers'){ 
							oid=o.name;
							if(v=='1'){
								$('#'+oid).prop('checked',true);
							}else{
								$('#'+oid).prop('checked',false);
							}
						}
						else if(o.name=='show_in_ptportal'){
							oid=o.name;
							if(v=='1'){
								$('#'+oid).prop('checked',true);
							}else{
								$('#'+oid).prop('checked',false);
							}
						}
						else if(o.name=='refdig'){							
							oid=o.name;
							if(v=='1'){
								$('#'+oid+'_y').prop('checked',true);
								$('#'+oid+'_n').prop('checked',false);
							}else{
								$('#'+oid+'_y').prop('checked',false);
								$('#'+oid+'_n').prop('checked',true);
							}
						}
						else if(o.name=='enable_hp'){							
							oid=o.name;
							if(v=='1'){
								$('#'+oid).prop('checked',true);
							}else{
								$('#'+oid).prop('checked',false);
							}
						}
						else{
							$('#'+oid).prop('checked',true);
						}
						
					} else if(o.type!='submit' && o.type!='button') {
						if(on=='facility_type'){
							$('#facility_type').html(fac_types);
							if(hq_exists > 0){
								if(v != 1){
									if($('#facility_type').find('.hq_hd').hasClass('hide') === false){
										$('#facility_type').find('.hq_hd').addClass('hide');
									}
								}
							}
							$('#facility_type').val(v);
							$('#facility_type').selectpicker('val',v);
							$('#facility_type').selectpicker('refresh');
							if(v==1){
								if($('#idone').hasClass('hide') === true){
									$('#idone').removeClass('hide');
								}
							}else{
								if($('#idone').hasClass('hide') === false){
									$('#idone').addClass('hide');
								}
							}
						}
						if(on=='facility_color'){
							try{
								if(v == '') v = '#ffffff';
								objcolor = dgn(on);
								objcolor.value = v;
								$('div.sp-preview-inner').css('background-color',v);
							}catch(e){
								alert(e.message);
							}
							//$(".grid_color_picker11").spectrum({color:colorVal1});
							/*$(".grid_color_picker11").spectrum({
								color:colorVal,	
								showInput: true,
								className: "full-spectrum",
								showInitial: true,
								showPalette: true,
								showSelectionPalette: true,
								showAlpha: true,
								maxPaletteSize: 10,
								preferredFormat: "hex",
								localStorageKey: "spectrum.demo",
							});*/
						}
						o.value = v;
					}
				}
			}
		}
		$('.selectpicker').selectpicker('refresh');
	}
	
	function saveFormData(){
		var chkData = checkdata();
		if(chkData=='1'){
			top.show_loading_image('hide');
			top.show_loading_image('show','300', 'Saving data...');
			frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
			if(!$('#show_in_ptportal').is(":checked")){frm_data += "&show_in_ptportal=0"}
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: frm_data,
				success: function(d) {
					top.show_loading_image('hide');
					//a=window.open(); a.document.write(d);
					if(d.toLowerCase().indexOf('success') > 0){
						top.alert_notification_show(d);
					}else{
						top.fAlert(d);
					}
					$('#addNew_div').modal('hide');
					LoadResultSet();
				}
			});
		}else{
			top.fAlert(msg);
		}
	}
	
	function zip_vs_state(objZip,objExt,objCity,objState,objCountry,objCounty){
		// window.opener.top.stop_zipcode_validation -> Not working yet as constant not declared 
		objCity = $('#'+objCity+'');
		objState = $('#'+objState+'');
		objExt = $('#'+objExt+'');
		zip_code = $(objZip).val();
		if(zip_code == ''){
			return false;
		}
		var url = top.JS_WEB_ROOT_PATH+"/interface/patient_info/ajax/demographics/add_city_state.php?zipcode="+zip_code+'&zipext=y';
		$.ajax({
			url:url,
			success:function(data)
			{
				var val=data.split("-");
				var city = $.trim(val[0]);
				var state = $.trim(val[1]);
				var extension = $.trim(val[2]);
				if(city!="")
				{
					$(objCity).val(city);
					$(objState).val(state);
					$(objExt).val(extension);
					return;
				}
				
				$(objZip).val("");
				fAlert('Please enter correct '+top.zipLabel);
				//changeClass(document.getElementById($(objZip).attr("id")),1);
				$(objZip).select();
				$(objCity).val(" ");
				$(objState).val(" ");
				$(objExt).val(" ");
			}
	   });
	}
	
	var del_reason="";
	function deleteSelectet(){
		top.show_loading_image('show');
		pos_id = '';
		$('.chk_sel').each(function(){
			if($(this).prop('checked')){
				pos_id += $(this).val()+', ';
			}
		});
		var reason_field="<br /><br />Reason: <textarea name='group_del_reason' id='group_del_reason' style='vertical-align:text-top;width:250px; overflow:auto;' onblur='window.top.fmain.onBlur_reason(this.value);'></textarea>";
		if(pos_id!=''){
			pos_id = pos_id.substr(0,pos_id.length-2);
			frm_data = 'pos_id='+pos_id+'&task=confirm_schedule';
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: frm_data,
				success: function(d) {
					top.show_loading_image('hide');
					var pre_msg="";
					if(d>0){
					pre_msg="One or more selected facilities have active future appointment(s), deleting related facility will result into moving all future appointment(s) to To-Do-Reschedule list. It will also remove all schedules for future and past. ";}
					else{pre_msg="All provider schedules (if any) for future and past, related to this facility will be deleted as well.";}
					top.fancyConfirm(pre_msg+"<br/> Are you sure you want to delete?"+reason_field,'',"window.top.fmain.deleteModifiers('"+pos_id+"')");
				}
			});
			
		
		}else{
			top.fAlert('No Record Selected.');
		}
	}
	function deleteModifiers(pos_id){
		
		var delReason;
		if($("#hidd_reason_text").length){delReason=$("#hidd_reason_text").val();}
		if($.trim(delReason)==""){top.fAlert('Please enter reason for deletion.');return false;	}
		//pos_id = pos_id.substr(0,pos_id.length-2);
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Deleting Record(s)...');
		frm_data = 'pkId='+pos_id+'&hidd_reason_text='+delReason+'&task=delete';
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: frm_data,
			success: function(d) {
				top.show_loading_image('hide');
				if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();$("#hidd_reason_text").val();}
				else{top.fAlert(d+' Record delete failed. Please try again.');}
			}
		});
	}
	function checkdata()
	{	
		msg = '';
		if(add_edit_frm.name.value=="")	
		{
			msg = msg + php_js_arr.fac_name; 
			$('#name').addClass('mandatory');
			//document.getElementById('name').className = 'mandatory';
			add_edit_frm.name.focus();
		}else{
			if($('#name').hasClass('mandatory') === true){
				$('#name').removeClass('mandatory');
			}
		}		
		
		var testresults = false;
		var str = document.add_edit_frm.ele_txt_1.value;

		if(str != ''){
			testresults = checkemail(str);
		}else{
			testresults = true;
		}
		if(testresults == false){
			msg = msg + php_js_arr.fac_email;
			document.add_edit_frm.ele_txt_1.focus();
		}
		if($("#fac_prac_code").val()==""){
			msg = msg+' - Please select POS facility.'
			$('#fac_prac_code').selectpicker('setStyle', 'btn-warning').selectpicker('refresh');
			//document.getElementById('fac_prac_code').className = 'mandatory';
		}else{
			$('#fac_prac_code').selectpicker('setStyle', 'btn-warning', 'remove').selectpicker('refresh');
		}
				
		if(msg == ''){
			return 1;
			//document.add_edit_frm.submit();	  
		}
		else{
			return 0;
		}
	}
	function checkemail(str){
		var filter=/^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i
		if (filter.test(str))
			testresults=true
		else{
			testresults=false
		}
		return (testresults)
	}
	
	function change_form(th)
	{
		if(th.value=='1'){
			if($('#idone').hasClass('hide') === true){
				$('#idone').removeClass('hide');
			}
			if(hq_exists=='1'){
				if($('#divLogoLink').hasClass('hide') === true){
					$('#divLogoLink').removeClass('hide');
				}
			}
		}else{
			if($('#idone').hasClass('hide') === false){
				$('#idone').addClass('hide');
			}
			if($('#divLogoLink').hasClass('hide') === false){
				$('#divLogoLink').addClass('hide');
			}		
		}
	}
	function set_phone_format(objPhone, default_format){
		//alert('new');
		if(objPhone.value == "" || objPhone.value.length < 10){
			//invalid_input_msg(objPhone, "Please Enter a valid phone number");
			top.fAlert("Please Enter a valid phone number",'',objPhone);
			objPhone.value = "";
		}else{
			var refinedPh = objPhone.value.replace(/[^0-9+]/g,"");					
			if(refinedPh.length < 10){
				//invalid_input_msg(objPhone, "Please Enter a valid phone number");
				top.fAlert("Please Enter a valid phone number",'',objPhone);
				objPhone.value= "";
			}else{
				switch(default_format){
					case "###-###-####":
						objPhone.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
					break;
					case "(###) ###-####":
						objPhone.value = "("+refinedPh.substring(0,3)+") "+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
					break;
					default:
						objPhone.value = refinedPh.substring(0,3)+"-"+refinedPh.substring(3,6)+"-"+refinedPh.substring(6,10);
					break;
				}
			}
		}
		//changeClass(objPhone);
	}
	function show_none_pos_fac(){
		frm_data = 'task=show_none_pos_fac';
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: frm_data,
			success: function(d) {
				if(d!=""){
					top.fAlert(d);
				}
			}
		});
	}
	function onBlur_reason(Reasonval){$("#hidd_reason_text").val(Reasonval);}
	
	/*****TO RELOAD LIST OF EMDEON FACILITIES******/
	function refresh_emdeon_facs(obj){
		top.show_loading_image('hide');
		top.show_loading_image('show','50', '<span class="white" style="color:#000">Refreshing Emdeon Facilities List...</span>');
		frm_data = 'task=refresh_emd_facs';
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: frm_data,
			beforeSend:function(){
				$(obj).addClass('spin');
			},
			success: function(d) {
				top.show_loading_image('hide');
				$(obj).removeClass('spin');
				if(d!=""){
					if(d.indexOf('<option')==0){
						$('#erx_facility_id').html(d);
						$('#erx_facility_id').selectpicker("refresh");
					}else{
						top.fAlert(d);
					}
				}else{
					
				}
			}
		});
	}
	
	function search_email(evnt,obj,div_id){
		var value = $(obj).val();
		if(value == ''){
			if($('#'+div_id+' select').hasClass('hide') === false){
				$('#'+div_id+'').find('select').addClass('hide');
				$(obj).val('');
			}
		}
		else if(value.search('@') > 0){
			var email = value.split('@');
			set_email_opt(email,div_id);
		}
	}

	function set_email_opt(email,div_id){
		var select_obj = $('#'+div_id+'').find('select');
		if($(select_obj).hasClass('hide') === true){
			$(select_obj).removeClass('hide');
		}
		$(select_obj).on('change',function(){
			var selectedVal = $(this).val();
			var final_email = email[0]+selectedVal;
			$('#'+div_id+'').parent().find('input').val(final_email);
			if($(select_obj).hasClass('hide') === false){
				$(select_obj).addClass('hide');
			}
		});
		
		$('#'+div_id+'').parent().find('input').on('blur',function(){
			if($(select_obj).hasClass('hide') === false){
				$(select_obj).addClass('hide');
			}
		});
		
	}
	
	$(function(){
		if($("#addNew_div").on('show.bs.modal')){
			var btn_array = [['Save','','top.fmain.saveFormData();']];
			top.fmain.set_modal_btns('addNew_div .modal-footer',btn_array);
		}
	});
	
	
	$(window).load(function(){
		LoadResultSet();
		show_none_pos_fac();
		var ar = [["add_new","Add New","top.fmain.addNew();"],
				  ["pos_del","Delete","top.fmain.deleteSelectet();"]
				 ];
		top.btn_show("ADMN",ar);
		$(document).ready(function(){
			check_checkboxes();
			set_header_title('Facility');
			$('[data-toggle="tooltip"]').tooltip();
			$('.selectpicker').selectpicker();
		});
		parent.show_loading_image('none');
	});
	
	$(window).resize(function(){
		set_modal_height('addNew_div');
	});