var arrAllShownRecords = new Array();
var totalRecords	   = 0;
var formObjects		   = new Array('procedure_id','procedure_name');
var cpt_code_array=new Array();var get_mod_code_arr=new Array();var get_dx_code_arr=[];
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading Procedure...');
	
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
	
	if(soAD=='ASC') $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
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
var consent_form_arr=new Array();var op_report_arr=new Array();
function showRecords(r){
	r = JSON.parse(r);
	result = r.records;
	consent_form=r.consent_form;
	op_report=r.op_report;
	cpt_code_arr=r.cpt_code_arr;
	dx_code_arr=r.dx_code_arr;
	get_med_name=r.get_med_name;
	get_mod_code=r.get_mod_code;
	
	var consent_options="";
	for(c in consent_form){
		cc=consent_form[c];
		consent_form_arr[cc.consent_form_id]=cc.consent_form_name.substr(0,23);
		consent_options+="<option value='"+cc.consent_form_id+"'>"+cc.consent_form_name+"</option>";
	}
	$("#consent_form_id").html(consent_options);
	
	var op_reports_options="";
	for(o in op_report){
		oo=op_report[o];
		op_report_arr[oo.temp_id]=oo.temp_name.substr(0,23);
		op_reports_options+="<option value='"+oo.temp_id+"'>"+oo.temp_name+"</option>";
	}
	$("#op_report_id").html(op_reports_options);
	
	var get_med_name_arr=new Array();
	var h=0;
	for(m in get_med_name){
		h++;
		mm=get_med_name[m];
		get_med_name_arr.push(mm.medicine_name);
	}
	
	var e=0;
	for(md in get_mod_code){
		e++;
		mm1=get_mod_code[md];
		get_mod_code_arr.push(mm1.mod_prac_code);
	}
	h='';var no_record='yes';
	if(r != null){
		row = '';
		for(x in result){no_record='no';
			s = result[x];
			rowData = new Array();
			row += '<tr>';						
			var tmphtml="";tmprow=[];
			for(y in s){
				
				tdVal = s[y];
				if(y=='procedure_id'){pkId = tdVal; tmprow[0] = '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='procedure_name'){
					tmprow[1]	= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='ret_gl'){
					if(tdVal==1){tdVal="Ret";}else if(tdVal==2){tdVal="GL";}else if(tdVal=="" || tdVal==3){tdVal="Other";}
					tmprow[2]	= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='cpt_code_str'){
					tmprow[4]	= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='dx_code'){
					tmprow[3]	= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='time_out_request'){
					tmprow[5]	= '<td style="text-transform:capitalize;" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='pre_op_meds'){
					if(tdVal){tdVal=tdVal.replace(/\|/g,"<div class='topborder' style='height:3px;'></div>&nbsp;");}
					tmprow[6]	= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='intraviteral_meds'){
					if(tdVal){tdVal=tdVal.replace(/\|/g,"<div class='topborder' style='height:3px;'></div>&nbsp;");}
					tmprow[7]	= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='post_op_meds'){
				if(tdVal){tdVal=tdVal.replace(/\|/g,"<div class='topborder' style='height:3px;'></div>&nbsp;");}
					tmprow[8]	= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
				}
				if(y=='consent_form_id'){
					var tdValShow=""
					if(tdVal.indexOf(",")>0){
						var arr_con=tdVal.split(",");
						for(var j=0;j<arr_con.length;j++){
							if(typeof consent_form_arr[arr_con[j]]!= "undefined"){
								tdValShow+=consent_form_arr[arr_con[j]]+"<div class='topborder' style='height:3px;'></div>";
							}
						}
						tdVal=tdValShow
					}else if(tdVal!='' && typeof consent_form_arr[tdVal]!="undefined"){tdVal=consent_form_arr[tdVal];}else{tdVal="";}
					tmprow[9]	= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
					
				}
				if(y=='op_report_id'){
					var tdValShow=""
					if(tdVal.indexOf(",")>0){
						var arr_con=tdVal.split(",");
						for(var j=0;j<arr_con.length;j++){
							if(typeof op_report_arr[arr_con[j]] != "undefined"){
								tdValShow+=op_report_arr[arr_con[j]]+"<div class='topborder' style='height:3px;'></div>";
							}
						}
						tdVal=tdValShow
					}else if(tdVal!='' && typeof op_report_arr[tdVal]!= "undefined"){tdVal=op_report_arr[tdVal];}else{tdVal="";}
					tmprow[10]	= '<td class="leftborder" onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				
			}
			
			tmphtml = ""+tmprow.join('');
			row += tmphtml;
			totalRecords++;
			row += '</tr>';
			if(typeof(pkId)!="undefined"){
				arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
			}
		}
		h = row;
	}
	if(no_record=='yes'){h+="<tr><td colspan='11' style='text-align:center;'>No Record Found</td></tr>";}
	$('#result_set').html(h);

	var g=0;
	for(c1 in cpt_code_arr){
		g++;
		cc1 = cpt_code_arr[c1];	
		cpt_code_array.push(cc1.cpt_prac_code);
	}
	var e=0;
	var dx_code_array = new Array();
	for(d in dx_code_arr){
		e++;
		dd1 = dx_code_arr[d];	
		dx_code_array.push({id:dd1.id,name:dd1.dx_code});
	}
	
	$("#dx_code").bind("change",function(){ checkValidDxCode(this); });
	$('[name^=dx_code]').each(function(id,elem){
		$(elem).typeahead({source:dx_code_array, onSelect:function(o){ get_dx_code_arr[""+o.value] = $.trim(o.text.replace(";","")); }});
	});		
	$('.med_atypehead').each(function(id,elem){
		$(elem).typeahead({source:get_med_name_arr});
	});
	$('.cpt_code_typeahead').each(function(id,elem){
		$(elem).typeahead({source:cpt_code_array});
	});
	$('.modfier_typeahead').each(function(id,elem){
		$(elem).typeahead({source:get_mod_code_arr});
	});
	
	
	$('.selectpicker').selectpicker('refresh');
	top.show_loading_image('hide');
}

function  checkValidDxCode(o){	
	var ttmp = get_dx_code_arr.slice();
	var a="", b="";
	var v = $.trim(o.value);
	if(v!=""){ 
		var av =  v.split(";");
		if(av.length>0){
			for(var x in av){
				var t = $.trim(av[x]);
				if(typeof(t)!="undefined" && t!=""){					
					if(typeof(ttmp)!="undefined"){
						for(var s in ttmp){							
							if($.trim(ttmp[s]) == t){								
								a+=t+";";
								b+=s+";";
								delete ttmp[s];
								break;
							}	
						}
					}
				}
			}
		}
		//var t = (av.length>1) ? av[av.length-1] : av[0];
		//t = $.trim(t);
		//$.get("ajax.php?p_dx="+encodeURI(t)+"&task=checkValidDxCode",function(d){ var tmp = "";if(av.length>1){ av = av.slice(0, -1); tmp = av.join(";")+";";  }if(d.flg=="OK"){ tmp += d.icd10+";";  }else{ tmp += ""; }o.value = $.trim(tmp);	},'json');
	}
	o.value = $.trim(a);
	$("#"+o.id+"_id").val($.trim(b));
	
	console.log("CHK", a, b);
	
}

function addNew(ed,pkId){
	var modal_title = '';
	$('#consent_form_id_sel').html("");
	$('#op_report_id_sel').html("");
	$("#intraviteral_meds").removeAttr("disabled");
	$("#auth_main_tbl tbody tr" ).each(function(){this.parentNode.removeChild( this ); });
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#procedure_id').val('');
		document.add_edit_frm.reset();
		addNewRow('');
	}
	$('#myModal .modal-header .modal-title').text(modal_title);
	$('#myModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
	$('.selectpicker').selectpicker('refresh');
}
function saveFormData(){
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Saving data...');
	frm_data = $('#add_edit_frm').serialize()+'&task=save_update';
	if($.trim($('#procedure_name').val())==""){
		top.fAlert("&bull; Enter the Procedure<br>");
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
	top.show_loading_image('hide');	
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
function fillEditData(pkId){
	f = document.add_edit_frm;
	e = f.elements;
	add_edit_frm.reset();$("#last_cnt").val("");
	$('#procedure_id').val(pkId);
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on	= o.name;
			//alert(arrAllShownRecords[]);
			v	= arrAllShownRecords[pkId][on];
			if(typeof(v)=='undefined'){v='';}
			if(on=='last_cnt'){
				if(v>0){
					var d;
					for(var c=1;c<=v;c++){
						addNewRow(c);
					}
				}else{
					addNewRow('');
				}
			}
			if(on=='pre_op_meds' || on=='intraviteral_meds' ||  on=='post_op_meds'){
				v=(v.replace(/\|/g,"\n"));	
			}
			if(on=='consent_form_id[]'){
				v	= arrAllShownRecords[pkId]['consent_form_id'];
				var c_optval='';
				if(v.indexOf(",")>0){
					v_arr = v.split(",");
					$('#consent_form_id').val(v_arr);
					if(v_arr.length>1){
						for(c=0;c<v_arr.length;c++){
							c_id=$.trim(v_arr[c]);
							if(typeof consent_form_arr[c_id] != "undefined"){
								c_optval+='<option value="'+c_id+'">'+consent_form_arr[c_id]+'</option>';	
							}
						}
					}
				}else if($.trim(v)){
					if(typeof consent_form_arr[v] != "undefined"){
						$('#consent_form_id').val(v);	
						c_optval='<option selected="selected" value="'+v+'">'+consent_form_arr[v]+'</option>';	
					}
				}
				$('#consent_form_id_sel').html(c_optval);
				continue;
			}
			if(on=='op_report_id[]'){
				v=arrAllShownRecords[pkId]['op_report_id'];
				if(v.indexOf(",")>0){
					v_arr=v.split(",");
					$('#op_report_id').val(v_arr);
					var c_optval='';
					if(v_arr.length>1){
						for(c=0;c<v_arr.length;c++){
							o_id=$.trim(v_arr[c]);
							if(typeof op_report_arr[o_id] != "undefined"){
								c_optval+='<option value="'+o_id+'">'+op_report_arr[o_id]+'</option>';	
							}
						}
					}
				}else if($.trim(v)){
					var c_optval;
					if(typeof op_report_arr[v] != "undefined"){
						$('#op_report_id').val(v);
						c_optval='<option selected="selected" value="'+v+'">'+op_report_arr[v]+'</option>';	
					}
				}
				$('#op_report_id_sel').html(c_optval);
				continue;
			}
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if (o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).prop('checked',true);
				} else if(o.type!='submit' && o.type!='button'){
					o.value = v;
				}else if(o.type!='submit' && o.type!='button'){
					if(on!='last_cnt')o.value = v;
				}
			}
			
		}
	}
	
	f = document.laser_add_edit;
	e = f.elements;
	laser_add_edit.reset();
	for(i=0;i<e.length;i++){
		o = e[i];
		if($.inArray(o.phrase,formObjects)){
			on	= o.name;
			//alert(arrAllShownRecords[]);
			v	= arrAllShownRecords[pkId][on];
			if(typeof(v)=='undefined'){v='';}
			if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA"){
				if (o.type == "checkbox" || o.type == "radio"){
					oid = on+'_'+v;
					$('#'+oid).prop('checked',true);
				} else if(o.type!='submit' && o.type!='button'){
					o.value = v;
				}else if(o.type!='submit' && o.type!='button'){
					if(on!='last_cnt')o.value = v;
				}
			}
			
		}
	}
	
	//dx code array
	get_dx_code_arr=[];
	var a = (""+$("#dx_code").val()).split(";"), b=(""+$("#dx_code_id").val()).split(";");
	
	console.log(a,b);
	
	if(a.length>0&&b.length>0){
		for(var aa=0;aa<a.length;aa++){
			var x=$.trim(b[aa]), y=$.trim(a[aa]);
			if(x!="" && y!=""){
			get_dx_code_arr[x] = y; 
			}
		}
	}
	
	
	
	$('.selectpicker').selectpicker('refresh');	
	$("#intraviteral_meds").removeAttr("disabled");
	if($("#laser_procedure_note_1").is(":checked")){$("#intraviteral_meds").attr("disabled","disabled");}
}
function addNewRow(pre_cnt){
	var cnt_t;
	var td_val=tr_val='';
	if(pre_cnt=="" || parseInt(pre_cnt)==0){
		pre_cnt=1;
	}else{
		cnt_t=$("#last_cnt").val();
		if(isNaN(cnt_t) || typeof(cnt_t)=="undefined" || cnt_t==""){cnt_t=0;}
		pre_cnt=(parseInt(cnt_t)+parseInt(1));
	}
	var pre_cnt1=parseInt(pre_cnt);
	var pre_cnt2=parseInt(pre_cnt);
	$("#last_cnt").val(pre_cnt);
	td_val ='<td><input type="text" class="cpt_code_typeahead form-control" data-provide="multiple" data-seperator="semicolon" name="cpt_code1_'+pre_cnt+'" id="cpt_code1_'+pre_cnt+'" ></td><td><input type="text" class="modfier_typeahead form-control" data-provide="multiple" data-seperator="semicolon" name="mod_code1_'+pre_cnt+'" id="mod_code1_'+pre_cnt+'"></td>';
	td_val+='<td><input type="text" class="cpt_code_typeahead form-control" data-provide="multiple" data-seperator="semicolon" name="cpt_code2_'+pre_cnt+'" id="cpt_code2_'+pre_cnt+'" ></td><td><input type="text" class="modfier_typeahead form-control" data-provide="multiple" data-seperator="semicolon" name="mod_code2_'+pre_cnt+'" id="mod_code2_'+pre_cnt+'"></td>';
	td_val+='<td><input type="text" class="cpt_code_typeahead form-control" data-provide="multiple" data-seperator="semicolon" name="cpt_code3_'+pre_cnt+'" id="cpt_code3_'+pre_cnt+'" onchange="addNewRow('+pre_cnt+')"></td><td><input type="text" class="modfier_typeahead form-control" data-provide="multiple" data-seperator="semicolon" name="mod_code3_'+pre_cnt+'" id="mod_code3_'+pre_cnt+'"></td>';
	td_val+='<td  style="text-align:center;white-space:nowrap;"><img id="add_row_'+pre_cnt+'" src=\"../../../../library/images/add_small.png\" onClick="addNewRow('+pre_cnt+');">&nbsp;&nbsp;&nbsp;<img id="add_row_'+pre_cnt+'" src=\"../../../../library/images/close_small.png\" onClick="removeTableRow('+pre_cnt+');"></td>';	
	tr_val ='<tr id="tr_'+pre_cnt+'">' + td_val + '</tr>';
		
	$("#auth_main_tbl").append(tr_val);
	$('#cpt_code1_'+pre_cnt).focus();
	$('[name^=cpt_code1_]').each(function(id,elem){
		$(elem).typeahead({source:cpt_code_array});
	});
	$('[name^=cpt_code2_]').each(function(id,elem){
		$(elem).typeahead({source:cpt_code_array});
	});
	$('[name^=cpt_code3_]').each(function(id,elem){
		$(elem).typeahead({source:cpt_code_array});
	});
	$('[name^=mod_code1_]').each(function(id,elem){
		$(elem).typeahead({source:get_mod_code_arr});
	});
	$('[name^=mod_code2_]').each(function(id,elem){
		$(elem).typeahead({source:get_mod_code_arr});
	});
	$('[name^=mod_code3_]').each(function(id,elem){
		$(elem).typeahead({source:get_mod_code_arr});
	});
}
function removeTableRow(id){
	$("#cpt_code1_"+id).val("");
	$("#cpt_code2_"+id).val("");
	$("#cpt_code3_"+id).val("");
	$("#mod_code1_"+id).val("");
	$("#mod_code2_"+id).val("");
	$("#mod_code3_"+id).val("");
	$("#tr_"+id).hide();
}

function get_input_fields(from_modal,to_modal,append_id){
	$('#'+from_modal+' .modal-body').find('textarea').each(function(id,elem){
		var val = $(elem).val();
		var elem_name = $(elem).attr('name');
		if(val != ''){
			var str = '<textarea name="'+elem_name+'">'+val+'</textarea>';
			$('#'+to_modal+' .modal-body #'+append_id).append(str);
		}
	});
	$('#'+from_modal).modal('hide');
}

var ar = [["add_new","Add New","top.fmain.addNew();"],["dx_cat_del","Delete","top.fmain.deleteSelectet();"]];
top.btn_show("ADMN",ar);
$(document).ready(function(){
	LoadResultSet();
	check_checkboxes();
	set_header_title('Procedures');	
	$("#laser_procedure_note_1").click(function(){
		$("#intraviteral_meds").removeAttr("disabled");
		if($("#laser_procedure_note_1").is(":checked")){$("#intraviteral_meds").attr("disabled","disabled");$("#laser_div").show("drop");}
	});
	$("#laser_procedure_note_label").click(function(){
		$('#laser_div').modal('show');
	});
	$('.selectpicker').selectpicker('refresh');
	$('body').on('shown.bs.modal','#myModal',function(){
		set_modal_height('myModal');
	});
});
show_loading_image('hide');