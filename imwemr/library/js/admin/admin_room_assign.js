	var arrAllShownRecords = facilities = new Array();
	var totalRecords	   = 0;
	var formObjects		   = new Array('id','mac_address','room_no','descs');
	
	function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Loading Room Details...');
		
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
	
	function showRecords(r){
		r = jQuery.parseJSON(r);
		result 		= r.records;
		facility 	= r.facilities;
		var fac_name_arr = new Array();
		fac_name_arr[0] = '';
		var fac_html = '';
		for(x in facility){
			facc = facility[x];
			fac_name_arr[facc.facid]=facc.facname;
			fac_html+='<option value="'+facc.facid+'">'+facc.facname+'</option>';
		}
		$("#fac_id").html(fac_html);
		$('#fac_id').selectpicker('refresh');
		
		h='';
		if(r != null){
			row = '';
			row_class = '';
			for(x in result){
				s = result[x];
				rowData = new Array();
				row += '<tr class="link_cursor'+row_class+'">';
				for(y in s){
					tdVal = s[y];
					if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
					rowData[y] = tdVal;
					if(y!='id'){
						if(y=='fac_id'){row	+= '<td class="leftborder alignLeft" onclick="addNew(1,\''+pkId+'\');">'+fac_name_arr[tdVal]+'</td>';}
						else{row	+= '<td class="leftborder alignLeft" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';}
					}
				}
				if(row_class==''){row_class=' alt';}else{row_class='';}
				totalRecords++;
				row += '</tr>';
				arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
			}
			h = row;
		}
		$('#result_set').html(h);		
		top.show_loading_image('hide');
	}
	
	function addNew(ed,pkId){
		var modal_title = '';
		if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
		else {
			modal_title = 'Add New Record';
			$('#id').val('');
			document.add_edit_frm.reset();
		}
		$('#myModal .modal-header .modal-title').text(modal_title);
		$('#myModal').modal('show');
		if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	}
	
	function fillEditData(pkId){
		f = document.add_edit_frm;
		e = f.elements;
		$('#id').val(pkId);
		for(i=0;i<e.length;i++){
			o = e[i];
			if($.inArray(o.name,formObjects)){
				on	= o.name;
				v	= arrAllShownRecords[pkId][on];
				if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
					if (o.type == "checkbox" || o.type == "radio") {
						oid = on+'_'+v;
						$('#'+oid).attr('checked',true);
					} else if(o.type!='submit' && o.type!='button') {
						o.value = v;
					}
				}
			}
		}
		$('#fac_id').selectpicker('refresh');
	}
	
	function saveFormData(){
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Saving data...');
		frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
		$.ajax({
            type: "POST",
            url: "ajax.php",
            data: frm_data,
            success: function(d) {
				top.show_loading_image('hide');
                if(d.toLowerCase().indexOf('success') > 0){
	                top.alert_notification_show(d);
				}else{
					top.fAlert(d);
				}
				$('#myModal').modal('hide');
				LoadResultSet();
            }
        });
	}
	
	function deleteSelectet(){
		pos_id = '';
		$('.chk_sel').each(function(){
			if($(this).is(':checked')){
				pos_id += $(this).val()+', ';
			}
		})
		if(pos_id!=''){
			top.fancyConfirm("Are you sure you want to delete?","","window.top.fmain.deleteModifiers('"+pos_id+"')");
		}else{
			top.fAlert('No Record Selected.');
		}
	}
	function deleteModifiers(pos_id) {
		pos_id = pos_id.substr(0,pos_id.length-2);
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Deleting Record(s)...');
		frm_data = 'pkId='+pos_id+'&task=delete';
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: frm_data,
			success: function(d) {
				top.show_loading_image('hide');
				if(d=='1'){top.alert_notification_show('Record Deleted'); LoadResultSet();}
				else{top.fAlert(d+'Record delete failed. Please try again.');}
			}
		});
	}

	function getPCName(){		
		try{
			var name = 'clientPCName';
			var chkCookie = document.cookie.indexOf(name + "=");
		
			if (chkCookie==-1){
				var net = new ActiveXObject("wscript.network"); 			   					
				var value = net.ComputerName; 	
				if(document.add_edit_frm.mac_address.value == "" && value != ""){
					get_matching_record(value);
				}

				document.add_edit_frm.room_no.focus();
				var date = new Date();
				date.setTime(date.getTime()+(1*24*60*60*1000));				
				var expires = "; expires="+date.toGMTString();
				setCookie(name,value,expires);					
			}else{

				var arr_cookies = document.cookie.split("; ");
				
				for(a=0; a<arr_cookies.length; a++){
					var cook_name_tmp = arr_cookies[a].split("=");
					if(cook_name_tmp[0] == "clientPCName"){
						//alert(cook_name_tmp[1]);
						var value = cook_name_tmp[1];
						if(document.add_edit_frm.mac_address.value == "" && value != ""){
							get_matching_record(value);
						}
					}
				}
			}
		}
		catch(err){
			 /*
			 txt="<b>There was an error on this page.</b><br>";
			  txt+="Error description: " + err.description + "<br><br>";
			  txt+="Please contact your administrator to resolve this error.<br><br>";
			  txt+="Click OK to continue.<br>";
			  fAlert(txt);
			  */
		}				
	}

	function get_matching_record(pc_name){
		frm_data = 'pc_name='+pc_name+'&task=match_record';
		$.ajax({
		type: "POST",
		url: "ajax.php",
		data: frm_data,
			success: function(resp){
				if(resp != '0'){
					var arr_resp = resp.split("~~|~~");
					document.add_edit_frm.mac_address.value = unescape(arr_resp[1]);					
					document.add_edit_frm.room_no.value = unescape(arr_resp[2]);
					var desc_str = unescape(arr_resp[3]);
					document.add_edit_frm.descs.value = desc_str.replace("+", " ");
				}
				document.add_edit_frm.mac_address.value =  pc_name;
			}
		});
	}

	function setCookie(c_name,value,exdays){
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : ";path=/; expires="+exdate.toUTCString());
		document.cookie=c_name + "=" + c_value;
	}

	function check_form(){
		if(document.add_edit_frm.mac_address.value == ""){
			document.add_edit_frm.mac_address.focus();
			parent.show_loading_image('none');
			fAlert("Please enter your PC Name");
			return false;
		}
		setCookie('clientPCName',document.add_edit_frm.mac_address.value,365)
		saveFormData();
	}
	
	
	
	$(document).ready(function(){
		LoadResultSet();
		
		var ar = [["add_new","Add New","top.fmain.addNew();"],["pos_del","Delete","top.fmain.deleteSelectet();"]];
		top.btn_show("ADMN",ar);
		
		check_checkboxes();
		set_header_title('Room Assign');
		
		getPCName();
		
		show_loading_image('none');
		$('.selectpicker').selectpicker();
	});
	