<?php
/*
// The MIT License (MIT)
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
?><?php require_once('../admin_header.php'); ?>
<!DOCTYPE html>
<html>
    <head>
        <title>imwemr</title>
        <meta name="viewport" content="width=device-width, maximum-scale=1.0" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

        <script type="text/javascript">
            $(document).ready(function () {
                set_header_title('Manage Lenses');
            });
            var arrAllShownRecords = new Array();
            var totalRecords = 0;
            var formObjects = new Array('heard_id', 'heard_options');
            function LoadResultSet(p, f, s, so, currLink) {//p=practice code, f=fac code, s=status, so=sort by;
                top.show_loading_image('hide');
                top.show_loading_image('show', '300', 'Loading IOLs(Lenses)...');

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
                $('.link_cursor span').removeAttr('class');
                if (soAD == 'ASC')
                    $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-down pull-right').addClass('glyphicon glyphicon-chevron-up pull-right');
                else
                    $(currLink).find('span').removeClass('glyphicon glyphicon-chevron-up pull-right').addClass('glyphicon glyphicon-chevron-down pull-right');

                so_url = '&so=' + so + '&soAD=' + soAD;

                ajaxURL = "ajax.php?task=show_list" + s_url + p_url + f_url + so_url;
                $.ajax({
                    url: ajaxURL,
                    success: function (r) {//a=window.open();a.document.write(r); ///*dataType: "json",*/
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
                    row_class = '';
                    for (x in result) {
                        no_record = 'no';
                        s = result[x];
                        rowData = new Array();
                        row += '<tr class="link_cursor' + row_class + '">';
                        for (y in s) {
                            tdVal = s[y];
                            //alert(y+' => '+tdVal);
                            if (y == 'iol_type_id') {
                                pkId = tdVal;
                                row += '<td class="text-center"><div class="checkbox"><input type="checkbox" name="id" id="chk_sel_' + tdVal + '" class="chk_sel" value="' + tdVal + '"><label for="chk_sel_' + tdVal + '">&nbsp;</label></div></td>';
                            }//alert(pkId+':'+y);
                            rowData[y] = tdVal;
                            if (y == 'lenses_iol_type') {
                                row += '<td onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + tdVal + '</td>';
                            }
                            if (y == 'lenses_category') {
                                row += '<td onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + tdVal + '</td>';
                            }
                            if (y == 'lenses_manufacturer') {
                                row += '<td onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + tdVal + '</td>';
                            }
                            if (y == 'lenses_brand') {
                                row += '<td onclick="addNew(1,\'' + pkId + '\');">&nbsp;' + tdVal + '</td>';
                            }
                        }
                        if (row_class == '') {
                            row_class = ' alt';
                        } else {
                            row_class = '';
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
                $("#iol_type_id").val('');
                if (typeof (ed) != 'undefined' && ed != '') {
                    $('#addNew_div .section_header label').text('Edit Record');
                } else {
                    $('#addNew_div .section_header label').text('Add New Record');
                    $('#heard_id').val('');
                    document.add_edit_frm.reset();
                    $('#addNew_div').modal('toggle');
                }
                $('#addNew_div, .dialogMask').fadeIn('fast');
                if ((typeof (ed) != 'undefined' && ed != '') && (typeof (pkId) != 'undefined' && pkId > 0)) {
                    $('#addNew_div').modal('toggle');
                    fillEditData(pkId);
                }
            }
            function saveFormData() {
                top.show_loading_image('hide');
                top.show_loading_image('show', '300', 'Saving data...');
                frm_data = $('#add_edit_frm').serialize() + '&task=save_update';
                var msg = "";
                if ($.trim($('#lenses_category').val()) == "") {
                    msg += "&bull; Please Fill Lens Category<br>";
                }
                if ($.trim($('#lenses_manufacturer').val()) == "") {
                    msg += "&bull; Please Fill Lens Manufacturer<br>";
                }
                if ($.trim($('#lenses_brand').val()) == "") {
                    msg += "&bull; Please Fill Lens Brand<br>";
                }
                if ($.trim($('#lenses_iol_type').val()) == "") {
                    msg += "&bull; Please Fill Lens IOL Type<br>";
                }
                if (msg != "") {
                    top.fAlert(msg);
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
                            top.fAlert('Record already exist.');
                            return false;
                        }
                        if (d.toLowerCase().indexOf('success') > 0) {
                            top.alert_notification_show(d);
                        } else {
                            top.fAlert(d);
                        }
                        $('#addNew_div').modal('toggle');
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
                    top.fancyConfirm("Are you sure you want to delete?", "", "top.fmain.deleteModifiers('" + pos_id + "')");
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
                add_edit_frm.reset();
                $('#heard_id').val(pkId);
                for (i = 0; i < e.length; i++) {
                    o = e[i];
                    if ($.inArray(o.phrase, formObjects)) {
                        on = o.name;
                        //alert(arrAllShownRecords[]);
                        v = arrAllShownRecords[pkId][on];
                        if (o.tagName == "INPUT" || o.tagName == "SELECT") {
                            if (o.type == "checkbox" || o.type == "radio") {
                                oid = on + '_' + v;
                                $('#' + oid).attr('checked', true);
                            } else if (o.type != 'submit' && o.type != 'button') {
                                o.value = v;
                            }
                        }
                    }
                }
            }
        </script>
    </head>
    <body class="body_c">
        <div class="whtbox">
            <input type="hidden" name="ord_by_field" id="ord_by_field" value="lenses_category">
            <input type="hidden" name="ord_by_ascdesc" id="ord_by_ascdesc" value="ASC">
            <div style="height:<?php print ($_SESSION['wn_height'] - 320); ?>px; overflow:auto; overflow-x:hidden;">
                <table class="table table-bordered adminnw tbl_fixed" width="100%">
                    <thead>
                        <tr class="page_block_heading_patch text12b text-center">
                            <th width="5%" class="text-center">
                                <div class="checkbox">
                                    <input type="checkbox" name="chk_sel_all" id="chk_sel_all" value="">
                                    <label for="chk_sel_all">&nbsp;</label>
                                </div>
                            </th>
                            <th onClick="LoadResultSet('', '', '', 'lenses_category', this);" class="link_cursor pl10">Lens Category<span></span></th>
                            <th onClick="LoadResultSet('', '', '', 'lenses_iol_type', this);" class="link_cursor pl10">&nbsp;Lens Iol Type<span></span></th>
                            <th onClick="LoadResultSet('', '', '', 'lenses_manufacturer', this);" class="link_cursor pl10">Lens Manufacturer<span></span></th>
                            <th onClick="LoadResultSet('', '', '', 'lenses_brand', this);" class="link_cursor pl10">Lens Brand<span></span></th>
                        </tr>
                    </thead>
                    <tbody id="result_set"></tbody>
                </table>
            </div>
            <div class="dialogMask hide"></div>

        </div>

        <div id="addNew_div" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <form name="add_edit_frm" id="add_edit_frm" style="margin:0px;" onSubmit="saveFormData();return false;">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title section_header"><label>Add New Record</label></h4>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="iol_type_id" id="iol_type_id" >
                            <table width="100%" cellpading="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="20%">Lens Category</td>
                                    <td width="80%"><input name="lenses_category" id="lenses_category" type="text" class="form-control mb5"></td>
                                </tr>
                                <tr>
                                    <td>Lens Iol Type</td>
                                    <td><input name="lenses_iol_type" id="lenses_iol_type" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td>Lens Manufacturer</td>
                                    <td><input name="lenses_manufacturer" id="lenses_manufacturer" type="text" class="form-control"></td>
                                </tr>
                                <tr>
                                    <td>Lens Brand</td>
                                    <td><input name="lenses_brand" id="lenses_brand" type="text" class="form-control"></td>
                                </tr>
                            </table>
                        </div>

                        <div id="module_buttons" class="ad_modal_footer modal-footer">
                            <button type="submit" class="btn btn-success" value="&#10004; Done">Done</button>
                            <button type="button" class="btn btn-danger" value="Cancel" data-dismiss="modal">Cancel</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </body>
    <script type="text/javascript">
        LoadResultSet();
        var ar = [["add_new", "Add New", "top.fmain.addNew();"],
            ["dx_cat_del", "Delete", "top.fmain.deleteSelectet();"]
        ];
        top.btn_show("ADMN", ar);
        $(document).ready(function () {
            $("thead.page_block_heading_patch tr td").attr({'title': 'Click to Sort Ascending/Descending'});
            $('.closeBtn').click(function () {
                $('#addNew_div, .dialogMask').fadeOut('fast');
            });
            $('#chk_sel_all').click(function () {
                if ($('#chk_sel_all').is(":checked")) {
                    $('.chk_sel').prop('checked', true);
                } else {
                    $('.chk_sel').prop('checked', false);
                }
            });
            $("#addNew_div").draggable();
        });
        parent.parent.show_loading_image('none');
    </script>

</html>
