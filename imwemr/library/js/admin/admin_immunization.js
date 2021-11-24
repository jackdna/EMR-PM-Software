var arrAllShownRecords = facilities = new Array();
	var totalRecords	   = 0;
	var formObjects		   = new Array('imnzn_id','imnzn_name','imnzn_type','imnzn_numberofdoses','imunz_mfr_code','imnzn_manufacturer','imnzn_ptalerts','imnzn_ptinstruction','register_immunization','imunz_cvx_coe','imunz_cpt_id','CPT_description');
	function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Loading Immunization...');
		
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
		
		//a=window.open(); a.document.write(ajaxURL);
		$.ajax({
		  url: ajaxURL,
		  success: function(r) {//a=window.open();a.document.write(r); ///*dataType: "json",*/
			showRecords(r);
		  }
		});
	}
	
	var arrCPTDetails=new Array();
	
	function showRecords(r){
		//$('#result_set').html(r+'<hr>end of Response');exit;
	 	r = jQuery.parseJSON(r);
		result 		= r.records;
		cpt_details 	= r.cpt_details;
		immunizationTypes = r.immunizationTypes;
		cpt_category = r.cpt_category;

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
					
					if(y=='imnzn_id'){pkId = tdVal; row += '<td style="width:2%"><div class="checkbox"><input type="checkbox" name="chk_sel" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
					rowData[y] = tdVal;
					if(y!='imnzn_id' && y!='imunz_cpt_id' && y!='imunz_mfr_code' && y!='imunz_cpt_id' && y!='CPT_description' && y!='cpt4_code'){
						if(y=='register_immunization'){
							if(tdVal=='0'){ tdVal='No';}
							if(tdVal=='1'){ tdVal='Yes';}
						}
						if(tdVal==null)
						{
							tdVal = '';
						}
						row	+= '<td class="leftborder alignLeft" style="padding-left:5px" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
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
		
		// IMMUN NAMES
		immun_names_opts = '<option value="">Select</option>';		
		for(x in cpt_details){
			ff = cpt_details[x];
			immun_names_opts += '<option value="'+ff.cpt_desc+'">'+ff.cvx_code+'-'+ff.cpt_desc+'</option>';
			arrCPTDetails[ff.cpt_fee_id] = ff.cvx_code+'~~'+ff.cpt_cat_id+'~~'+ff.cpt4_code+'~~'+ff.cpt_desc;
		}			
		$('#imnzn_name').html(immun_names_opts);
		
		// IMMUN TYPES
		immun_type_opts = '<option value="">- Select -</option>';		
		for(x in immunizationTypes){
			ff = immunizationTypes[x];
			immun_type_opts += '<option value="'+ff+'">'+ff+'</option>';
		}			
		$('#imnzn_type').html(immun_type_opts);
		
		// CPT Categories
		cpt_type_opts = '';
		for(x in cpt_category){
			ff = cpt_category[x];
			cpt_type_opts += '<option value="'+x+'">'+ff+'</option>';
		}			
		$('#imunz_cpt_id').html(cpt_type_opts);
	}
	
	function addNew(ed,pkId){
		var modal_title = '';
		if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
		else {
			modal_title = 'Add New Record';
			$('#imnzn_id').val('');
			document.add_edit_frm.reset();
		}
		$('#myModal .modal-header .modal-title').text(modal_title);
		$('#myModal').modal('show');
		if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	}
	
	function fillEditData(pkId){
		f = document.add_edit_frm;
		e = f.elements;
		$('#imnzn_id').val(pkId);
		for(i=0;i<e.length;i++){
			o = e[i];
			if($.inArray(o.name,formObjects)){
				on	= o.name;
				v	= arrAllShownRecords[pkId][on];
				if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
					if (o.type == "checkbox" || o.type == "radio") {
						if(v==1){
							$('#'+on).attr('checked',true);
						}else{
							$('#'+on).attr('checked',false);
						}
					} else if(o.type!='submit' && o.type!='button') {
						//alert(o.name+' ~ '+v);
						o.value = v;
					}
				}
			}
		}
			$('#imnzn_type').selectpicker('refresh');
			//$('#imunz_cpt_id').selectpicker('refresh');
	}
	
	function saveFormData(){
		top.show_loading_image('hide');
		top.show_loading_image('show','300', 'Saving data...');
		
		frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
		
		var msg="";
		if($.trim($('#imnzn_name').val())==""){
			msg+="&nbsp;&bull;&nbsp;Please Select Name<br>";
		}
		
		if(msg){
			msg_val="<b>Please fill the following&nbsp;:-</b><br>"+msg;
			top.fAlert(msg_val);
			top.show_loading_image('hide');
			return false;
		}
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
				hideDoseDiv();
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
	
	function fillValues(cpt_id){
		var arrDet = (arrCPTDetails[cpt_id]).split('~~');

		$('#imunz_cvx_coe').val(arrDet[0]);
		$('#imunz_cpt_id').val(arrDet[1]);
		$('#elem_cpt4Code').val(arrDet[2]);
		
	}

	function setImmunizationOptions(){
		var doseNumber = 0; 
		var imnznID = 0; 
		var data_call = $("#imnzn_numberofdoses").data('call');
		
		if(!isNaN($("#imnzn_numberofdoses").val())){
			doseNumber = $("#imnzn_numberofdoses").val();
		}
		
		if(!isNaN($("#imnzn_id").val())){
			imnznID = $("#imnzn_id").val();
		}
		
		if(doseNumber>0 && data_call == 'no'){
			top.show_loading_image('show');
			frm_data = "task=get_immu_opts&doseNumber="+doseNumber+"&imnznID="+imnznID;
			$.ajax({
				type: "POST",
				url: "ajax.php",
				data: frm_data,
				success: function(d) {
					top.show_loading_image('hide');
					if(d){
						top.show_loading_image('hide');
						$("#showImmunizationOptions").show();
						$("#showImmunizationOptions").html(d);
						$("#imnzn_numberofdoses").data('call','yes');						
					}
				}
			});
		}
	}

	function setImmunizationsValue(strAllVal){
		if(strAllVal!=""){
			if(strAllVal.indexOf('~~')!=-1){
				var ArrayForVals=strAllVal.split("~~");

				if(ArrayForVals[0]>0){
					$('#imunz_cpt_id').val(ArrayForVals[0]);
				}
				if(ArrayForVals[1]){
					$("#imunz_cvx_coe").val(ArrayForVals[1]);
				}
				if(ArrayForVals[2]){
					$("#imnzn_name").val(ArrayForVals[2]);
				}
				if(ArrayForVals[3]){
					$("#cpt4_code").val(ArrayForVals[3]);
				}
			}
		}
	}

	function hideDoseDiv(){
		$('#showImmunizationOptions').empty().hide();
		$("#imnzn_numberofdoses").data('call','no');	
	}
	
	$(function(){
		$('#myModal').on('hide.bs.modal',function(){
			hideDoseDiv()
		});
		
		$('body').on('shown.bs.modal','#myModal',function(){
			set_modal_height('myModal');
		});
	});
	
	var ar = [["add_new","Add New","top.fmain.addNew();"],["pos_del","Delete","top.fmain.deleteSelectet();"]];
	top.btn_show("ADMN",ar);
	
	$(document).ready(function(){
		LoadResultSet();
		check_checkboxes();
		set_header_title('Immunization');
		$('[data-toggle="tooltip"]').tooltip();
    });
	show_loading_image('none');