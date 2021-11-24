var arrAllShownRecords = new Array();
var arrAllShownMerchantRecords = new Array();
var totalRecords	   = 0;
var totalMerchantRecords	   = 0;
var formObjects		   = new Array('id','merchant_id','facility_id','deviceName','deviceID','developerID','applicationID','ipAddress','port');
var formMerchantObjects		   = new Array('id','merchantName','mid','userID','mid_paswrd','Company','api_url');
function LoadResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading POE...');
	
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
			
	ajaxURL = "ajax.php?task=show_devices_list"+s_url+p_url+f_url+so_url;
	$.ajax({
	  url: ajaxURL,
	  success: function(r){
		showRecords(r);
	  }
	});
}

function LoadMerchantResultSet(p,f,s,so,currLink){//p=practice code, f=fac code, s=status, so=sort by;
	top.show_loading_image('hide');
	top.show_loading_image('show','300', 'Loading POE...');
	
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
			
	ajaxURL = "ajax.php?task=show_merchant_list"+s_url+p_url+f_url+so_url;
	$.ajax({
	  url: ajaxURL,
	  success: function(r){
		showMerchantRecords(r);
	  }
	});
}

function showMerchantRecords(r){
	r = jQuery.parseJSON(r);
	result = r.merchant;
	h='';
	if(r != null){
		row = '';
		row_class = '';
		for(x in result){
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
				if(y=='merchantName'){
					row	+= '<td onclick="addNewMerchant(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
				}
				if(y=='mid'){
					row	+= '<td onclick="addNewMerchant(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='userID'){
					row	+= '<td onclick="addNewMerchant(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
				}
//				if(y=='mid_paswrd'){
//					row	+= '<td onclick="addNewMerchant(1,\''+pkId+'\');">&nbsp;&nbsp;</td>';
//				}
				if(y=='Company'){
					row	+= '<td onclick="addNewMerchant(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
				}
//				if(y=='api_url'){
//					row	+= '<td onclick="addNewMerchant(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
//				}
			}
			totalMerchantRecords++;
			row += '</tr>';
            
			arrAllShownMerchantRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}

	$('#tsys_merch_result_set').html(h);		
	top.show_loading_image('hide');
}


function showRecords(r){
	r = jQuery.parseJSON(r);
	result = r.records;
    var merchant = r.merchant;
    var facilityArr = r.facilityArr;
	h='';
	if(r != null){
		row = '';
		row_class = '';
		for(x in result){
			s = result[x];
			rowData = new Array();
			row += '<tr>';
			for(y in s){
				tdVal = s[y];
				if(y=='id'){pkId = tdVal; row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';}
				rowData[y] = tdVal;
                if (y == 'merchant_id') {
                    row += '<td class="text-left" style="width:18%" onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + merchant[tdVal] + '</td>';
                }
                if (y == 'facility_id') {
                    row += '<td class="text-left" style="width:18%" onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + facilityArr[tdVal] + '</td>';
                }
								if (y == 'SETVAR') {
                    row += '<td class="text-left" style="width:5%">';
										row += '<button class="btn btn-success" type="button" data-muser= "'+tdVal.m_user+'" data-mpass="'+tdVal.m_pass+'" data-mid="'+tdVal.mid+'" data-did="'+tdVal.did+'" data-durl="'+tdVal.d_url+'"  onClick="SetPOSVar(this);">Set Machine Vars</button>';
										row += '</td>';
                }
				if(y=='deviceName'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
				}
				if(y=='deviceID'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">'+tdVal+'</td>';
				}
				if(y=='developerID'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
				}
				if(y=='applicationID'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
				}
				if(y=='ipAddress'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
				}
				if(y=='port'){
					row	+= '<td onclick="addNew(1,\''+pkId+'\');">&nbsp;&nbsp;'+tdVal+'</td>';
				}
			}
			totalRecords++;
			row += '</tr>';
            
			arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
		}
		h = row;
	}
	$('#tsys_result_set').html(h);		
	top.show_loading_image('hide');
}

function addNew(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#id').val('');
		document.add_edit_frm.reset();
        $('.selectpicker').selectpicker('refresh');
	}
	$('#deviceModal .modal-header .modal-title').text(modal_title);
	$('#deviceModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditData(pkId);}
}

function fillEditMerchantData(pkId) {
    f = document.merchant_add_edit_frm;
	if(f){document.merchant_add_edit_frm.reset();}
    e = f.elements;
    //$('#mrchnt_id').val(pkId);
    
    for (i = 0; i < e.length; i++) {
        o = e[i];
        if ($.inArray(o.name, formMerchantObjects)) {
            on = o.name;

            v = arrAllShownMerchantRecords[pkId][on];
            if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA") {
                if (o.type != 'submit' && o.type != 'button') {
                    o.value = v;
                }
            }
        }
    }
    $('#mrchnt_id').val(pkId);
    //$('.selectpicker').selectpicker('refresh');
}

function fillEditData(pkId) {
    f = document.add_edit_frm;
	if(f){ document.add_edit_frm.reset(); }
    e = f.elements;
    $('#id').val(pkId);

    for (i = 0; i < e.length; i++) {
        o = e[i];
        if ($.inArray(o.name, formObjects)) {
            on = o.name;

            v = arrAllShownRecords[pkId][on];
            if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA") {
                if (o.type != 'submit' && o.type != 'button') {
                    o.value = v;
                }
            }
        }
    }
    $('.selectpicker').selectpicker('refresh');
}

function checkdata(from) {
    var formObjectsArr = { merchant_id : 'Merchant',facility_id : 'Facility',deviceName : 'Device Name',deviceID : 'Device ID',developerID : 'Developer ID',applicationID : 'Application ID',ipAddress : 'IP Address',port : 'Port Number'};
    if(from) {
        var formObjectsArr = { merchantName : 'Merchant Name',mid : 'MID',userID : 'User ID',mid_paswrd : 'Merchant Password',Company : 'Company',api_url : 'API URL'};
    }
    msg = '';
    $.each(formObjectsArr, function(key, elem) {
        if ($('#'+key).length > 0) {
            if ($('#'+key).val() == '') {
                msg += 'Please enter '+elem+'.<br>';
            }
        }
    });

    if (msg == '') {
        return true;
    } else {
        top.fAlert(msg);
        return false;
    }
    
}


function saveFormData() {
    var chkData = checkdata();
    if (chkData) {
        top.show_loading_image('hide');
        top.show_loading_image('show', '300', 'Saving data...');
        frm_data = $('#add_edit_frm').serialize() + '&task=save_update';
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: frm_data,
            success: function (d) {
                top.show_loading_image('hide');
                if (d.toLowerCase().indexOf('success') > 0) {
                    top.alert_notification_show(d);
                } else {
                    top.fAlert(d);
                }
                $('#addNew_div').modal('hide');
                window.location.reload();
            }
        });
    }
}


function saveMerchantData() {
    var chkData = checkdata(1);
    if (chkData) {
        top.show_loading_image('hide');
        top.show_loading_image('show', '300', 'Saving data...');
        frm_data = $('#merchant_add_edit_frm').serialize() + '&task=save_update_merchant';
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: frm_data,
            success: function (d) {
                top.show_loading_image('hide');
                if (d.toLowerCase().indexOf('success') > 0) {
                    top.alert_notification_show(d);
                } else {
                    top.fAlert(d);
                }
                $('#merchantModal').modal('hide');
                window.location.reload();
            }
        });
    }
}


function addNewMerchant(ed,pkId){
	var modal_title = '';
	if(typeof(ed)!='undefined' && ed!=''){modal_title = 'Edit Record';}
	else {
		modal_title = 'Add New Record';
		$('#mrchnt_id').val('');
		document.merchant_add_edit_frm.reset();
	}
	$('#merchantModal .modal-header .modal-title').text(modal_title);
	$('#merchantModal').modal('show');
	if((typeof(ed)!='undefined' && ed!='') && (typeof(pkId)!='undefined' && pkId>0)){fillEditMerchantData(pkId);}
}
	

$('#chk_sel_all').on('click', function() {
    if($('#chk_sel_all').is(":checked")) {
        $('.chk_sel').prop('checked', true);
    } else {
        $('.chk_sel').prop('checked', false);
    }
});


function deleteSelected() {
    var device_ids = '';
	$('.chk_sel').each(function(){
		if($(this).is(':checked')){
			device_ids += $(this).val()+', ';
		}
	});
    
    if(device_ids!=''){
		top.fancyConfirm("Are you sure you want to delete?","","top.fmain.deleteModifiers('"+device_ids+"')");
	}else{
		top.fAlert('No Record Selected.');
	}
}

function deleteSelectedMerchant() {
    var merchant_ids = '';
	$('.chk_sel').each(function(){
		if($(this).is(':checked')){
			merchant_ids += $(this).val()+', ';
		}
	});
    
    if(merchant_ids!=''){
		top.fancyConfirm("Are you sure you want to delete?","","top.fmain.deleteMerchantModifiers('"+merchant_ids+"')");
	}else{
		top.fAlert('No Record Selected.');
	}
}

function deleteMerchantModifiers(merchant_ids) {
    merchant_ids = merchant_ids.substr(0,merchant_ids.length-2);
    frm_data = 'merchant_ids='+merchant_ids+'&task=delete_merchant';
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: frm_data,
        success: function(d) {
            top.show_loading_image('hide');
            top.fAlert(d);
            window.location.reload();
        }
    });
}
function deleteModifiers(device_ids) {
    device_ids = device_ids.substr(0,device_ids.length-2);
    frm_data = 'device_ids='+device_ids+'&task=delete';
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: frm_data,
        success: function(d) {
            top.show_loading_image('hide');
            top.fAlert(d);
            window.location.reload();
        }
    });
}


function showDevicesList(){
    window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/pos/pos_index.php';
}
function showMerchantList(){
    window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/pos/pos_merchant_index.php';
}

var ar = [["add_new","Add New","top.fmain.addNew();"],["add_merchant","Add Merchant","top.fmain.addNewMerchant();"],["pos_del","Delete","top.fmain.deleteSelectet();"]];
//var ar = [["add_new","Add New","top.fmain.addNew();"],["pos_del","Delete","top.fmain.deleteSelected();"]];
top.btn_show("ADMN",ar);
//$(document).ready(function(){
//LoadResultSet();
////check_checkboxes();
//
//});
show_loading_image('none');