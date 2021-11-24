	var arrAllShownRecords = new Array();
	var totalRecords	   = 0;
	var formObjects		   = new Array('phrase_id','phrase');
	function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Loading ...');
		
		if(typeof(s)!='string' || s==''){s = 'Active';}
		s_url = "&s="+s;
		
		if(typeof(p)=='undefined'){p_url='';}else{p_url='&p='+p};
		if(typeof(f)=='undefined'){f_url='';}else{f_url='&f='+f};
		pro_id	= $('#provider_id').val();
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
		
		$('.link_cursor span').removeAttr('class');
		if(soAD=='ASC')	$(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
		else $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');
		
		so_url='&so='+so+'&soAD='+soAD+'&provider_id='+pro_id;
		ajaxURL = "ajax.php?task=show_list"+s_url+p_url+f_url+so_url;
		$.ajax({
		  url: ajaxURL,
		  success: function(r) {
			showRecords(r);
			$('.selectpicker').selectpicker('refresh');
		  }
		});
	}
	function showRecords(r){
		r = jQuery.parseJSON(r);
		result = r.records;
		providers = r.providers;
		$('#sel_provider_id').html(providers);
		h='';var no_record='yes';
		if(r != null){
			row = '';
			row_class = '';
			for(x in result){no_record='no';
				s = result[x];
				rowData = new Array();
				row += '<tr class="link_cursor'+row_class+'">';
				for(y in s){
					tdVal = s[y];
					if(y=='id'){pkId = tdVal; row += '<td><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
					rowData[y] = tdVal;
					if(y=='status_text'){
						row	+= '<td colspan="2" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
					}
					if(y=='status_color'){
						row	+= '<td  class="leftborder" onclick="addNew(1,\''+pkId+'\');"><p style="background-color:#'+tdVal+';width:80%;text-align:center;margin:0 auto">&nbsp;</p></td>';
					}
				}
				if(row_class==''){row_class=' alt';}else{row_class='';}
				totalRecords++;
				row += '</tr>';
				arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
			}
			h = row;
		}
		if(no_record=='yes'){h+="<tr><td colspan='4' style='text-align:center;'>No Record Found</td></tr>";}
		$('#result_set').html(h);		
		top.show_loading_image('hide');
	}

	function addNew(ed,pkId){
		var modal_title = '';
		if(typeof(ed)!='undefined' && ed!=''){
			modal_title = 'Edit Record';
		} else {
			modal_title = 'Add New Record';
			$('#id').val('');
			document.add_edit_frm.reset();
		}
		$('#myModal .modal-header .modal-title').text(modal_title);
		$('#myModal').modal('show');
		$("#myModal").on('shown.bs.modal', function(){
			$('#status_text').focus();
		});
		
		if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	}

	function saveFormData(){
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Saving data...');
		frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
		if($.trim($('#status_text').val())==""){
			top.fAlert("Enter The \"Ready For\" text.");
			top.show_loading_image('hide');
			return false;
		}
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: frm_data,
			success: function(d) {
				top.show_loading_image('hide');
				if(d=='enter_unique'){
					top.fAlert('Record already exist.');		
					return false;
				}
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
		});
		if(pos_id!=''){
			top.fancyConfirm("Are you sure you want to delete?","", "window.top.fmain.deleteModifiers('"+pos_id+"')");
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
	function fillEditData(pkId){
		f = document.add_edit_frm;
		e = f.elements;
		add_edit_frm.reset();
		$('#id').val(pkId);
		for(i=0;i<e.length;i++){
			o = e[i];
			if($.inArray(o.phrase,formObjects)){
				on	= o.name;
				//alert(arrAllShownRecords[]);
				v	= arrAllShownRecords[pkId][on];
				if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
					if (o.type == "checkbox" || o.type == "radio"){
						oid = on+'_'+v;
						$('#'+oid).attr('checked',true);
					} else if(o.type!='submit' && o.type!='button'){
						o.value = v;
						if(on=='status_color' && v!='')$('.bfh-colorpicker-icon').css('background-color','#'+v);
					}
				}
			}
		}
	}
	
	function save_imon_basic_settings(o){
		if(typeof(o)=='undefined') {top.fAlert(typeof(o)); return;}
		otag	= o.tagName;
		oname 	= o.name;
		oid		= o.id;
		ot		= o.type;
		
		casevalue = '';
		if (otag == "INPUT" || otag == "SELECT" || otag == "TEXTAREA"){
			if (ot == "checkbox" || ot == "radio"){
				if($('#'+oid).prop('checked')) casevalue = o.value; else casevalue = 0;	
				if(oid=='auto_refresh' && casevalue==0){
					$('#refresh_in_background').prop('disabled',true);
					$('#refresh_interval').prop('disabled',true);
				}else if(oid=='auto_refresh' && casevalue==1){
					$('#refresh_in_background').prop('disabled',false);
					$('#refresh_interval').prop('disabled',false);
				}
				
				if(oname=='refresh_in_background' && casevalue==0) casevalue='NO';			
			} else if(ot!='submit' && ot!='button'){
				casevalue = o.value;
			}
		}
		
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Saving changes...');
		frm_data = 'task=save_imon_settings&casename='+oname+'&casevalue='+escape(casevalue);
		$.ajax({
			type: "POST",
			url: "ajax.php",
			data: frm_data,
			success: function(d) {
				top.show_loading_image('hide');
				if(d.toLowerCase().indexOf('success') < 0){
					top.fAlert('Saving Failed.');		
					return false;
				}
				if(d.toLowerCase().indexOf('success') > 0){
					top.alert_notification_show(d);
				}else{
					top.fAlert(d);
				}
			}
		});
	}
	
	
	var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
	top.btn_show("ADMN",ar);
	
	$(document).ready(function(){
		top.fmain.LoadResultSet();
		check_checkboxes();
		set_header_title('IMedic Monitor');
	});
	
	show_loading_image('hide');
	$('.selectpicker').selectpicker();