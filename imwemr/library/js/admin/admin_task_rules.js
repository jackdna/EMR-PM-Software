var arrAllShownRecords = rules = new Array();
var totalRecords = 0;
var formObjects = new Array('id', 'tm_rule_name', 'tm_rcat_id');

function LoadResultSet(p, f, s, so, currLink) {//p=practice code, f=fac code, s=status, so=sort by;
    top.show_loading_image('hide');
    top.show_loading_image('show', '300', 'Loading Rules...');

    if (typeof (s) != 'string' || s == '') {
        s = 'Active';
    }
    s_url = "&s=" + s;

    if (typeof (p) == 'undefined') {
        p_url = '';
    } else {
        p_url = '&p=' + p;
    }
    
    if (typeof (f) == 'undefined') {
        f_url = '';
    } else {
        f_url = '&f=' + f;
    }
    

    oso = $('#ord_by_field').val(); //old_so
    soAD = $('#ord_by_ascdesc').val();
    if (typeof (so) == 'undefined' || so == '') {
        so = $('#ord_by_field').val();
    } else {
        $('#ord_by_field').val(so);
        if (oso == so) {
            if (soAD == 'ASC')
                soAD = 'DESC';
            else
                soAD = 'ASC';
        } else {
            soAD = 'ASC';
        }
        $('#ord_by_ascdesc').val(soAD);
    }
    
    //so 		= 'pos_prac_code';
    $('.link_cursor span').removeAttr('class');
    if (soAD == 'ASC')
        $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
    else
        $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');

    so_url = '&so=' + so + '&soAD=' + soAD;
    ajaxURL = "ajax.php?task=show_list" + s_url + p_url + f_url + so_url;

    $.ajax({
        url: ajaxURL,
        success: function (r) {
            showRecords(r);
        }
    });
}

var hq_fac_id = '';
function showRecords(r) {
    r = JSON.parse(r);
    //console.log(r);
    var rules = r.rules;
    var categories = r.categories;

    h = '';
    if (r != null) {
        var row = '';
        for (x in rules) {
            s = rules[x];
            rowData = new Array();
            row += '<tr class="">';
            $.each(s, function (y, tdVal) {
                rowData[y] = tdVal;
                if (y == 'id') {
                    pkId = tdVal;
                    //row += '<td width="5%"><div class="checkbox checkbox-inline"><input type="checkbox" name="id" id="'+tdVal+'" class="chk_sel" value="'+pkId+'"><label for="'+tdVal+'"></label></div></td>';
                }
                if (y == 'tm_rcat_id') {
                    row += '<td class="text-left" style="width:18%" onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + categories[tdVal] + '</td>';
                }
                if (y == 'tm_rule_name') {
                    row += '<td class="text-left" style="width:18%" onclick="addNew(1,\'' + pkId + '\');">' + tdVal + '</td>';
                }
//					if(y=='tm_rule_alias'){
//						row	+= '<td class="leftborder alignLeft" style="width:18%" onclick="addNew(1,\''+pkId+'\');">&nbsp;'+tdVal+'</td>';
//					}
            });
            row += '<td class="text-left" style="width:18%" onclick="addNewOptions(1,\'' + pkId + '\');">Rule Options</td>';
            row += '</tr>';
            arrAllShownRecords[pkId] = rowData;
        }
        h = row;
    }

    $('#result_set').html(h);
    top.show_loading_image('hide');

}

function addNew(ed, pkId) {
    if (typeof (ed) != 'undefined' && ed != '') {
        $('#addNew_div .modal-header .modal-title').text('Edit Record');

    } else {
       // $('#addNew_div .modal-header .modal-title').text('Add New Record');

       // $('#id').val('');
       // document.add_edit_frm.reset();

        //$('.selectpicker').selectpicker('refresh');
    }
    $('#addNew_div').modal('show');

    //$('.selectpicker').selectpicker('refresh');
    //set_modal_height('addNew_div');
    if ((typeof (pkId) != 'undefined' && pkId > 0)) {
        fillEditData(pkId);
    }
}


function fillEditData(pkId) {
    f = document.add_edit_frm;
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

function saveFormData() {
    var chkData = checkdata();
    if (chkData == '1') {
        top.show_loading_image('hide');
        top.show_loading_image('show', '300', 'Saving data...');
        frm_data = $('#add_edit_frm').serialize() + '&task=save_update';
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: frm_data,
            success: function (d) {
                top.show_loading_image('hide');
                //a=window.open(); a.document.write(d);
                if (d.toLowerCase().indexOf('success') > 0) {
                    top.alert_notification_show(d);
                } else {
                    top.fAlert(d);
                }
                $('#addNew_div').modal('hide');
                //LoadResultSet();
                window.location.reload();
            }
        });
    } else {
        top.fAlert(msg);
    }
}

function checkdata() {
    msg = '';
    if ($('#tm_rcat_id').val() == '') {
        msg += 'Please Select Rule Category.'
    }
    if ($('#tm_rule_name').val() == '') {
        msg += 'Please Enter Rule Name.'
    }
    if (msg == '') {
        return 1;
    } else {
        return 0;
    }
}


function showActiveTab(obj) {
    $('.tab-pane').hide();
    var currentTab = $(obj).attr('href');
    var cat_id = currentTab.replace('#cat_tab','');
    $('#cat_id').val(cat_id);
    
    $(currentTab).siblings('li').removeClass(' active ');
    $(currentTab).addClass(' active ');
    $(currentTab).show();
}

function editRuleCheck() {
    return false;
    var r_id = $('#rule_id').val();
    var obid='';
    if ($('.rule_box').find("input:checked").length!=1) {
        top.fAlert("Please select a rule to edit.");
        return false;
    } else {
        $(".rule_box").find("input:checked").each(function (i, ob) {
            obid=ob.id;
            r_id = obid.replace('rule_','');
            $('#rule_id').val(r_id);
        });
    }
    if(!r_id) {
        top.fAlert("Please select a rule to edit.");
        return false;
    }
    addNew(1,r_id);
}



function showOptionsforRule(obj, cat_id, rule_id, ss_type, list_id) {
    top.show_loading_image('show');
    //$('.ar_aging_val_div').hide();
    var parent_li = $(obj).parents('li');
    if ($(obj).is(":checked")) {
        if ($('.rule_box').find("input:checked").length!=1) {
            $('.rule_box').find("input:checked").prop('checked', false);
            $('[class^=ruleoption_div_]').html('');
        }
        if(!list_id){
            $('#ar_aging_value'+rule_id).val('');
            $('#list_id').val('');
            $('#comment').val('');
            $('#user_name').val('');
            $('#user_group').val('');
        }
        $(obj).prop('checked', true);
        parent_li.siblings('li').removeClass('active');
        parent_li.addClass(' active ');
    } else {
        $(obj).prop('checked', false);
        parent_li.removeClass(' active ');
        $('.ruleoption_div_'+rule_id).html('');
        $('#ar_aging_value'+rule_id).val('');
        top.show_loading_image('hide');
        return false;
    }
    var frm_data = {action: 'get_options', cat_id: cat_id, rule_id: rule_id, ss_type:ss_type, list_id:list_id};
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: frm_data,
        success: function (d) {
            $('.ruleoption_div_'+rule_id).html(d);

            $('#rule_id').val(rule_id);
            top.show_loading_image('hide');
            $('.selectpicker').selectpicker('refresh').on('shown.bs.select', function(){
                $('[data-toggle="tooltip"]').tooltip();
            });
            
            if(cat_id==3) {
                var val = $('#ar_aging_value'+rule_id).val();
                //if(val!='')ar_aging_show_hide(rule_id,val);
                $('#ar_aging'+rule_id).attr("onChange", "ar_aging_trigger(this);" );
            }
            
        }
    });

}

function ar_aging_show_hide(rule_id,val) {
    if(val=='any') {
        //$('.ar_aging_div_'+rule_id).hide();
        $('#ar_aging_value'+rule_id).val('');
        return false;
    } else {
        $('.ar_aging_div_'+rule_id).show();
    }
}
function ar_aging_trigger(obj) {
    var rule_id = $(obj).data('rule_id');
    var val = $(obj).val();
    //ar_aging_show_hide(rule_id,val);
}


$(function () {
    if ($("#addNew_div").on('show.bs.modal')) {
        var btn_array = [['Save', '', 'top.fmain.saveFormData();']];
        top.fmain.set_modal_btns('addNew_div .modal-footer', btn_array);
    }
});

if(page_view=='rule_manager') {
    $(document).ready(function () {
        set_header_title('Rule Manager');

        $('.rulesleft li a').on('click', function (e) {
            e.preventDefault();
            showActiveTab(this);
        });

    });

    $(window).on('load', function () {
        var obj = $('.rulesleft li.active').find('a');
        showActiveTab(obj);

        //var ar = [["new_rule", "New Rule", "top.fmain.create_rule();"],["assign_rule", "Save", "top.fmain.save_rule();"],ar_btn];
        var ar = [["assign_rule", "Save", "top.fmain.save_rule();"],ar_btn];

        top.btn_show("ADMN", ar);

        //LoadResultSet();
        parent.show_loading_image('none');
        ar_aging_trigger();
    });

}

function save_rule() {
    if ($('.rule_box').find("input:checked").length == 0) {
        top.fAlert("Please select a rule.");
        return false;
    }
    if ($('#user_group').find("option:selected").length == 0 && $('#user_name').find("option:selected").length == 0) {
        top.fAlert("Please select Group OR User.");
        return false;
    }
    var rule_id = $('#rule_id').val();
    var cat_id = $('#cat_id').val();

    if(cat_id == 1 && (rule_id==2 || rule_id==3) && $('#reason_code'+rule_id).find("option:selected").length == 0) {
        top.fAlert("Please select reason code.");
        return false;
    }
    
    if(cat_id == 1 && (rule_id==7) && $('#patientStatus'+rule_id).find("option:selected").length == 0) {
        top.fAlert("Please select patient status.");
        return false;
    }
    if(cat_id == 1 && (rule_id==8) && $('#pt_account_status'+rule_id).find("option:selected").length == 0) {
        top.fAlert("Please select patient account status.");
        return false;
    }
    
    if(cat_id == 2) {
        var alert_msg='';
        if($('#appt_procedure'+rule_id).find("option:selected").length == 0) {
            alert_msg+="Please select procedure.<br>";
        }
        if($('#sel_provider'+rule_id).find("option:selected").length == 0 || $('#appt_ref_phy'+rule_id).find("option:selected").length == 0 || $('#facilities'+rule_id).find("option:selected").length == 0) {
            alert_msg+="Please select Physician/Ref. Physician/Location.";
        }
        if(alert_msg!='') {
            top.fAlert(alert_msg);
            return false;
        }
    }
    
    
    if(cat_id == 3) {
        var alert_msg='';
        if($('#ar_aging'+rule_id).val() == '') {
            alert_msg+="Please select A/R Aging days.<br>";
        }
        if(($('#ar_aging'+rule_id).val() != '' && $('#ar_aging_value'+rule_id).val() == '')) {
            alert_msg+="Please enter A/R Aging amount.<br>";
        }
        if($('#ar_group'+rule_id).find("option:selected").length == 0) {
            alert_msg+="Please select Group.<br>";
        }
        if($('#ar_facility'+rule_id).find("option:selected").length == 0) {
            alert_msg+="Please select Facility.<br>";
        }
        if($('#ar_ins_group'+rule_id).find("option:selected").length == 0) {
            alert_msg+="Please select insurance group.<br>";
        }
        if($('#ar_ins_comp'+rule_id).find("option:selected").length == 0) {
            alert_msg+="Please select insurance company.<br>";
        }
        if(alert_msg!='') {
            top.fAlert(alert_msg);
            return false;
        }
    }
    
    top.show_loading_image('show');
    var frm_data = $('#assign_rule_frm').serialize()+'&action=submit_form';
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: frm_data,
        success: function (d) {
            top.show_loading_image('hide');
            if(d) {fAlert(d);}
            show_rule_list();
        }
    });
}

function show_rule_list(page) {
    var url='';
    if(page){
        url='?page='+page;
    }
    window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/taskrules/rule_listing.php'+url;
}


function load_rule_edit(list_id) {
    create_rule(list_id);
}

function create_rule(list_id){
    var url='';
    if(list_id){
        url='?list_id='+list_id;
    }
    window.location.href= top.JS_WEB_ROOT_PATH+'/interface/admin/taskrules/rule_manager.php'+url;
}

$('#chk_sel_all').on('click', function() {
    if($('#chk_sel_all').is(":checked")) {
        $('.chk_sel').prop('checked', true);
    } else {
        $('.chk_sel').prop('checked', false);
    }
});




function deleteRule() {
    var rule_list_id = '';
	$('.chk_sel').each(function(){
		if($(this).is(':checked')){
			rule_list_id += $(this).val()+', ';
		}
	});
    
    if(rule_list_id!=''){
		top.fancyConfirm("Are you sure you want to delete?","","top.fmain.deleteModifiers('"+rule_list_id+"')");
	}else{
		top.fAlert('No Record Selected.');
	}
}

function deleteModifiers(rule_list_id) {
    rule_list_id = rule_list_id.substr(0,rule_list_id.length-2);
    frm_data = 'listId='+rule_list_id+'&task=delete';
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

