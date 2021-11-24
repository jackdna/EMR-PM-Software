var arrAllShownRecords = new Array();
var totalRecords = 0;
var formObjects = new Array('folder_id', 'folder_name', 'folder_status');
function LoadResultSet(p, f, s, so, currLink) {//p=practice code, f=fac code, s=status, so=sort by;
    top.show_loading_image('hide');
    top.show_loading_image('show', '300', 'Loading Folders...');

    if (typeof (s) != 'string' || s == '') {
        s = 'Active';
    }
    s_url = "&s=" + s;

    if (typeof (p) == 'undefined') {
        p_url = '';
    } else {
        p_url = '&p=' + p
    }
    ;
    if (typeof (f) == 'undefined') {
        f_url = '';
    } else {
        f_url = '&f=' + f
    }
    ;

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
    ;
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
function showRecords(r) {
    r = jQuery.parseJSON(r);
    result = r.records;
    h = '';
    var no_record = 'yes';
    if (r != null) {
        row = '';
        for (x in result) {
            no_record = 'no';
            s = result[x];
            rowData = new Array();
            row += '<tr>';
            for (y in s) {
                tdVal = s[y];
                if (y == 'folder_id') {
                    pkId = tdVal;
                    row += '<td style="width:20px; padding-left:10px;"><div class="checkbox"><input type="checkbox" name="id" id="' + tdVal + '" class="chk_sel" value="' + pkId + '"><label for="' + tdVal + '"></label></div></td>';
                }
                rowData[y] = tdVal;
                if (y == 'folder_name') {
                    row += '<td onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + tdVal + '</td>';
                }
                if (y == 'folder_status') {
                    row += '<td style=\"text-transform:capitalize;\" onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + tdVal + '</td>';
                }
            }
            totalRecords++;
            row += '</tr>';
            arrAllShownRecords[pkId] = rowData;//this array will be used to fill edit record data in form.
        }
        h = row;
    }
    if (no_record == 'yes') {
        h += "<tr><td colspan='3' style='text-align:center;'>No Record Found</td></tr>";
    }
    
    $('#result_set').html(h);
    top.show_loading_image('hide');
}

function addNew(ed, pkId) {
    var modal_title = '';
    if (typeof (ed) != 'undefined' && ed != '') {
        modal_title = 'Edit Record';
    } else {
        modal_title = 'Add New Record';
        $('#folder_id').val('');
        document.add_edit_frm.reset();
    }
    $('#myModal .modal-header .modal-title').text(modal_title);
    $('#myModal').modal('show');
    if ((typeof (ed) != 'undefined' && ed != '') && (typeof (pkId) != 'undefined' && pkId > 0)) {
        fillEditData(pkId);
    }
}

function saveFormData() {
    top.show_loading_image('hide');
    top.show_loading_image('show', '300', 'Saving data...');
    frm_data = $('#add_edit_frm').serialize() + '&task=save_update';
    var folder = $('#folder_name').val();
    if ($.trim($('#folder_name').val()) == "") {
        top.fAlert("Enter The folder name");
        top.show_loading_image('hide');
        return false;
    }
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: frm_data,
        success: function (d) {
            top.show_loading_image('hide');
            if (d == 'enter_unique') {
                top.fAlert("'<b>" + folder + "</b>' folder already exists.");
                return false;
            }
            if (d.toLowerCase().indexOf('success') > 0) {
                top.alert_notification_show(d);
            } else {
                top.fAlert(d);
            }
            $('#myModal').modal('hide');
            LoadResultSet();
        }
    });
}
function deleteSelectet() {
    pos_id = '';
    $('.chk_sel').each(function () {
        if ($(this).is(':checked')) {
            pos_id += $(this).val() + ', ';
        }
    })
    if (pos_id != '') {
        top.fancyConfirm("Are you sure you want to delete?", "", "window.top.fmain.deleteModifiers('" + pos_id + "')");
    } else {
        top.fAlert('No Record Selected.');
    }
}
function deleteModifiers(pos_id) {
    pos_id = pos_id.substr(0, pos_id.length - 2);
    top.show_loading_image('hide');
    top.show_loading_image('show', '300', 'Deleting Record(s)...');
    frm_data = 'pkId=' + pos_id + '&task=delete';
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: frm_data,
        success: function (d) {
            top.show_loading_image('hide');
            if (d == '1') {
                top.alert_notification_show('Record Deleted');
                LoadResultSet();
            } else {
                top.fAlert(d + 'Record delete failed. Please try again.');
            }
        }
    });
}
function fillEditData(pkId) {
    f = document.add_edit_frm;
    e = f.elements;
    f.reset();
    $('#folder_id').val(pkId);
    for (i = 0; i < e.length; i++) {
        o = e[i];
        if ($.inArray(o.phrase, formObjects)) {
            on = o.name;
            v = arrAllShownRecords[pkId][on];
            if (o.tagName == "INPUT" || o.tagName == "SELECT" || o.tagName == "TEXTAREA") {
                if (o.type == "checkbox" || o.type == "radio") {
                    oid = on + '_' + v;
                    $('#' + oid).prop('checked', true);
                } else if (o.type != 'submit' && o.type != 'button') {
                    o.value = v;
                }
            }
        }
    }
}

var ar = [["add_new", "Add New", "top.fmain.addNew();"], ["dx_cat_del", "Delete", "top.fmain.deleteSelectet();"]];
top.btn_show("ADMN", ar);

$(document).ready(function () {
    LoadResultSet();
    check_checkboxes();
    set_header_title('Messages Folder');
});
show_loading_image('none');