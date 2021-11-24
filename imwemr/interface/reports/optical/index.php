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
?>
<?php
/*
  FILE : index.php
  PURPOSE : OPTICAL ORDER DETAIL
  ACCESS TYPE : INCLUDED
 */

require_once("../reports_header.php");
include_once($GLOBALS['fileroot'] . '/library/classes/SaveFile.php');
require_once('../../../library/classes/class.reports.php');
require_once('../../../library/classes/cls_common_function.php');
require_once('../common/report_logic_info.php');

$CLSCommonFunction = new CLSCommonFunction;
$CLSReports = new CLSReports;

//---Get Global Date Format
$date_format_SQL = get_sql_date_format();
$phpDateFormat = phpDateFormat();

$patientSearch = $_REQUEST['patientSearch'];
$searchKeyWord = $_POST['txt_findBy'];
$patientId = $_POST["patientId"];
$patient = $_POST["patient"];

$cl_order = false;
if ($_REQUEST['showpage']) {
    $showpage == $_REQUEST['showpage'];
    switch ($showpage) {
        case 'contactlens':
            $title = 'Contact Lens';
            break;
        case 'glasses':
            $title = 'Glasses Reports';
            break;
        case 'contactlensorder':
            $title = 'Contact Lens Order';
            break;
    }
}
if (empty($_REQUEST['Start_date']) == true && empty($_REQUEST['End_date']) == true) {
    $_REQUEST['Start_date'] = $_REQUEST['End_date'] = date($phpDateFormat);
}

if($_POST['form_submitted']){
    $strPhysician='';
    if ($_REQUEST['Physician'] || $_GET['selPhysicians']) {
        $strPhysician = implode(',', $_POST['Physician']);
        if($_GET['selPhysicians']) {
            $strPhysician = $_GET['selPhysicians'];
        }
    }
    $selFacilities='';
    if($_REQUEST['sc_name'] || $_GET['selFacilities']) {
        $selFacilities = implode(',', $_POST['sc_name']);
        if($_GET['selFacilities']) {
            $selFacilities = $_GET['selFacilities'];
        }
    }
}


//--- GET PHYSICIAN DROP DOWN ----
$physician_drop = $CLSCommonFunction->drop_down_providers($strPhysician, '', '', '', 'report');
$allPhyCount = sizeof(explode('</option>', $physician_drop)) - 1;

//--- GET FACILITY SELECT BOX ----
$selArrFacility= array_combine($_REQUEST['sc_name'],$_REQUEST['sc_name']);
$fac_query = "select id,name from facility order by name";
$fac_query_res = imw_query($fac_query);
$fac_id_arr = array();
$facilityName = "";
while ($fac_res = imw_fetch_array($fac_query_res)) {
	$sel='';
    $fac_id = $fac_res['id'];
    $fac_id_arr[$fac_id] = $fac_res['name'];
	if($selArrFacility[$fac_id])$sel='SELECTED';

    $facilityName .= '<option value="'.$fac_res['id'].'" '.$sel.'>' . $fac_res['name'] . '</option>';
}
$fac_cnt=sizeof($fac_id_arr);

if($_REQUEST['showpage'] == 'contactlensorder') {
    //--- GET FACILITY NAME ----
	$facilityName = $CLSReports->getFacilityName($selArrFacility, '1');
	$facilityName.='<option value="0">Not Defined</option>';
    $allFacCount = sizeof(explode('</option>', $facilityName)) - 1;
}

if ($_POST['form_submitted']) {

    //DATE RANGE ARRAY WEEKLY/MONTHLY/QUARTERLY
    $arrDateRange = $CLSCommonFunction->changeDateSelection();
    if ($dayReport == 'Daily') {
        $Start_date = $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Weekly') {
        $Start_date = $arrDateRange['WEEK_DATE'];
        $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Monthly') {
        $Start_date = $arrDateRange['MONTH_DATE'];
        $End_date = date($phpDateFormat);
    } else if ($dayReport == 'Quarterly') {
        $Start_date = $arrDateRange['QUARTER_DATE_START'];
        $End_date = $arrDateRange['QUARTER_DATE_END'];
    }
    //---------------------
    $phpDateFormat = phpDateFormat();
    $curDate = date($phpDateFormat . ' h:i A');
    $op_name_arr = preg_split('/, /', strtoupper($_SESSION['authProviderName']));
    $createdBy = ucfirst(trim($op_name_arr[1][0]));
    $createdBy .= ucfirst(trim($op_name_arr[0][0]));
}


$dbtemp_name = ucfirst($title);
//CSV NAME
$dbtemp_name_CSV = strtolower(str_replace(" ", "_", $dbtemp_name)) . ".csv";

//$patientSearch = core_get_patient_search_controls($_SESSION['authId'],"document.getElementById('loading_img')",'patient','findBy','../../common/core_search_functions.php');
$statusOptions = array('Pending', 'Ordered', 'Received', 'Dispensed');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>:: imwemr ::</title>
        <!-- Bootstrap -->
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
              <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
              <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
            <![endif]-->

        <style>
            .pd5.report-content {
                position:relative;
                margin-left:40px;

                background-color: #EAEFF5;
            }
            .fltimg {
                position:absolute;
            }
            .fltimg span.glyphicon {
                position: absolute;
                top: 170px;
                left: 10px;
                color: #fff;
            }
            .reportlft .btn.btn-mkdef {
                padding-top: 6px;
                padding-bottom: 6px;
            }
            #content1{
                background-color:#EAEFF5;
            }
            .total-row {
                height: 1px;
                padding: 0px;
                background: #009933;
            }	
        </style>
    </head>
    <body>
        <form name="frm_reports" id="frm_reports" action="" method="post">
            <input type="hidden" name="form_submitted" id="form_submitted" value="1">
            <input type="hidden" name="page_name" id="page_name" value="<?php echo $dbtemp_name; ?>">
            <div class=" container-fluid">
                <div class="anatreport">
                    <div id="physician_drop" style="position:absolute;bottom:0px;"></div>
                    <div id="facility_drop" style="position:absolute;bottom:0px;"></div>
                    <div class="row" id="row-main">
                        <div class="col-md-3" id="sidebar">
                            <div class="reportlft" style="height:100%;">
                                <div class="practbox">
                                    <div class="anatreport"><h2>Practice Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">

                                        <div class="row">
                                            <?php if ($_REQUEST['showpage'] == 'contactlens' || $_REQUEST['showpage'] == 'contactlensorder') { ?>
                                                <div class="col-sm-6">
                                                    <label>Users</label>
                                                    <select name="Physician[]" id="Physician" class="selectpicker" data-container="#physician_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                        <?php echo $physician_drop; ?>
                                                    </select>
                                                </div>
                                                <div class="col-sm-6">
                                                    <label>Facility</label>
                                                    <select name="sc_name[]" id="sc_name" class="selectpicker" data-container="#facility_drop" data-width="100%" data-size="10" multiple data-actions-box="true" data-title="Select All">
                                                        <?php echo $facilityName; ?>
                                                    </select>
                                                </div>
                                            <?php } ?>
                                            <?php if ($_REQUEST['showpage'] != 'contactlens') { ?>
                                                <div class="col-sm-6">
                                                    <label>Order Status</label>
                                                    <select name="orderMainStatus" id="orderMainStatus" class="selectpicker" data-width="100%" data-size="10" data-actions-box="true" data-title="Select All">
                                                        <?php if($_REQUEST['showpage'] != 'contactlensorder') { ?><option value='-1' <?php echo (($_POST['orderMainStatus'] == '-1')?'selected="selected"':''); ?> >Show All</option><?php } ?>
                                                        <?php
                                                        foreach ($statusOptions as $key => $val) {
                                                            $sel = "";
                                                            if ($orderMainStatus == $key) {
                                                                $sel = "selected";
                                                            }
                                                            echo"<option value='$key' $sel >$val</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            <?php } ?>
                                            <div class="col-sm-12">
                                                <label>Period</label>
                                                <div id="dateFieldControler">
                                                    <select name="dayReport" id="dayReport" class="selectpicker" data-width="100%" data-actions-box="false" onchange="DateOptions(this.value);">
                                                        <option value="Daily" <?php if ($_POST['dayReport'] == 'Daily') echo 'selected="selected"'; ?>>Daily</option>
                                                        <option value="Weekly" <?php if ($_POST['dayReport'] == 'Weekly') echo 'selected="selected"'; ?>>Weekly</option>
                                                        <option value="Monthly" <?php if ($_POST['dayReport'] == 'Monthly') echo 'selected="selected"'; ?>>Monthly</option>
                                                        <option value="Quarterly" <?php if ($_POST['dayReport'] == 'Quarterly') echo 'selected="selected"'; ?>>Quarterly</option>
                                                        <option value="Date" <?php if ($_POST['dayReport'] == 'Date') echo 'selected="selected"'; ?>>Date Range</option>
                                                    </select>
                                                </div>
                                                <div class="row" style="display:none" id="dateFields">
                                                    <div class="col-sm-5">
                                                        <div class="input-group">
                                                            <input type="text" name="Start_date" placeholder="From" style="font-size: 12px;" id="Start_date" value="<?php echo $_REQUEST['Start_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="Start_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>	
                                                    <div class="col-sm-5">	
                                                        <div class="input-group">
                                                            <input type="text" name="End_date" placeholder="To" style="font-size: 12px;" id="End_date" value="<?php echo $_REQUEST['End_date']; ?>" class="form-control date-pick">
                                                            <label class="input-group-addon" for="End_date"><span class="glyphicon glyphicon-calendar" aria-hidden="true"></span></label>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <button type="button" class="btn" onclick="DateOptions('x');"><span class="glyphicon glyphicon-arrow-left"></span></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if ($_REQUEST['showpage'] != 'contactlensorder') { ?>
                                <div class="appointflt">
                                    <div class="anatreport"><h2>Analytic Filter</h2></div>
                                    <div class="clearfix"></div>
                                    <div class="pd5" id="searchcriteria">
                                        <div class="row">
                                            <div class="col-sm-12"><label>Search Patient</label></div>
                                        </div> 
                                        <div class="row">
                                            <div class="col-sm-5">
                                                <input type="hidden" name="patientId" id="patientId" value="<?php //echo $patientId;   ?>">
                                                <input placeholder="Patient" type="text" id="patient" name="patient" onkeypress="{
                                                            if (event.keyCode == 13)
                                                                return searchPatient()
                                                        }" class="form-control" onblur="searchPatient(this)" value="<?php //echo $patient; ?>">
                                            </div> 
                                            <div class="col-sm-4">
                                                <select name="txt_findBy" id="txt_findBy" onkeypress="{
                                                            if (event.keyCode == 13)
                                                                return searchPatient()
                                                        }" class="form-control minimal">
                                                    <option value="Active" <?php if ($_POST['txt_findBy'] == 'Active') echo 'selected="selected"'; ?>>Active</option>
                                                    <option value="Inactive" <?php if ($_POST['txt_findBy'] == 'Inactive') echo 'selected="selected"'; ?>>Inactive</option>
                                                    <option value="Deceased" <?php if ($_POST['txt_findBy'] == 'Deceased') echo 'selected="selected"'; ?>>Deceased</option> 
                                                    <option value="Resp.LN" <?php if ($_POST['txt_findBy'] == 'Resp.LN') echo 'selected="selected"'; ?>>Resp.LN</option> 
                                                    <option value="Ins.Policy" <?php if ($_POST['txt_findBy'] == 'Ins.Policy') echo 'selected="selected"'; ?>>Ins.Policy</option>
                                                </select>
                                            </div> 
                                            <div class="col-sm-3 text-left">
                                                <button class="btn btn-success btn-sm" type="button" onclick="searchPatient();">Search</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                                <?php if ($_REQUEST['showpage'] == 'contactlens') { ?>
                                    <div class="practbox">
                                        <div class="clearfix"></div>
                                        <div class="pd5" id="searchcriteria">
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <div class="checkbox pointer">
                                                        <input type="checkbox" name="orderMainStatus" id="orderMainStatus" value="cl_order"/> 
                                                        <label for="orderMainStatus">CL Req</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="grpara">
                                    <div id="module_buttons" class="ad_modal_footer text-center" style="position:absolute; bottom:0;width:100%;">
                                        <button class="savesrch" type="button" onClick="top.fmain.get_report()">Search</button>
                                    </div>
                                </div>                                                                                        
                            </div>
                        </div>
                        <div class="col-md-9" id="content1">
                            <div class="btn-group fltimg" role="group" aria-label="Controls">
                                <img class="toggle-sidebar" src="../../../library/images/practflt.png" alt=""/><span class="glyphicon glyphicon-chevron-left"></span>
                            </div>
                            <div class="pd5 report-content">
                                <div class="rptbox">
                                    <div id="html_data_div" class="row">
                                        <?php
                                        if ($_POST['form_submitted'] || $_GET['confirm_id']) {
                                            include($showpage . '.php');
                                        } else {
                                            echo '<div class="text-center alert alert-info">No Search Done.</div>';
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <form name="csvDownloadForm" id="csvDownloadForm" action="../downloadFile.php" method ="post" > 
            <input type="hidden" name="csv_text" id="csv_text">	
            <input type="hidden" name="csv_file_name" id="csv_file_name" value="<?php echo $dbtemp_name_CSV; ?>" />
        </form> 

        <script type="text/javascript">
            var dbtemp_name = '<?php echo $dbtemp_name; ?>';
            var optical_showpage = '<?php echo $showpage; ?>';
            var cl_order = '<?php echo $cl_order; ?>';
            if(cl_order) {
                $('#orderMainStatus').prop('checked', true);
            }
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $(document).ready(function () {
                $(".fltimg").click(function () {
                    $("#sidebar").toggleClass("collapsed");
                    $("#content1").toggleClass("col-md-12 col-md-9");

                    if ($('.fltimg').find('span').hasClass('glyphicon glyphicon-chevron-left')) {
                        $('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-left').addClass('glyphicon glyphicon-chevron-right');
                    } else {
                        $('.fltimg').find('span').removeClass('glyphicon glyphicon-chevron-right').addClass('glyphicon glyphicon-chevron-left');
                    }
                    return false;
                });


            });

            var file_location = '<?php echo $file_location; ?>';
            var printFile = '<?php echo $printFile; ?>';
            var show_remove_btn = '<?php echo $show_remove_btn; ?>';

            //BUTTONS
            var mainBtnArr = new Array();
            <?php if ($_REQUEST['showpage'] != 'contactlensorder') { ?>
                if (printFile == '1') {
                    mainBtnArr[0] = new Array("print", "Print PDF", "top.fmain.generate_pdf();");
                    mainBtnArr[1] = new Array("start_process", "Export CSV", "top.fmain.export_csv();");
                }
            <?php } else { ?>
                if(show_remove_btn) {
                    mainBtnArr[0] = new Array("cl_order_status", "<?php echo ($order=='0')?'Remove':$order;?>", "top.fmain.statusSubmit();");
                }
            <?php }  ?>
                top.btn_show("PPR", mainBtnArr);
                //-------
                
                
            function searchPatient() {
                $("#patientId").val('');
                var name = document.getElementById("patient").value;
                var findBy = document.getElementById("txt_findBy").value;
                var validate = true;
                if (name.indexOf('-') != -1) {
                    name = name.replace(' ', '');
                    name = name.split('-');
                    name = name[0]
                    validate = false;
                }
                if (validate) {
                    if (isNaN(name)) {
                        pt_win = window.open("../../../interface/scheduler/search_patient_popup.php?btn_enter=" + findBy + "&btn_sub=" + name + "&call_from=physician_console", "mywindow", "width=800,height=500,scrollbars=yes");
                    } else {
                        getPatientName(name);
                    }
                }

                return false;
            }

            function getPatientName(id, obj) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $GLOBALS['webroot']; ?>/interface/physician_console/ajax_html.php?from=console&task=pt_details_ajax&return_data=yes&ptid=" + id,
                    dataType: 'JSON',
                    success: function (r) {
                        if (r.id) {
                            if (obj) {
                                set_xml_modal_values(r.id, r.pt_name);
                            } else {
                                $("#patient").val(r.pt_name);
                                $("#patientId").val(r.id);
                            }
                        } else {
                            fAlert("Patient not exists");
                            $("#patient").val('');
                            return false;
                        }
                    }
                });
            }

            function physician_console(id, name) {
                $("#patient").val(name);
                $("#patientId").val(id);

            }

            function get_report() {
                top.show_loading_image('hide');
                top.show_loading_image('show');

                if ($('#searchCriteria').val() == '') {
                    if ($('#chkSaveSearch').prop('checked') == true) {
                        toggle_lightbox();
                        //$('#divLightBox').toggle('slow');
                        return false;
                    }
                    if (lightBoxFlag && $('#report_name').val() == '') {
                        alert('Please enter search name');
                        return false;
                    } else if (lightBoxFlag && $('#report_name').val() != '') {
                        for (i = 0; i < arrSearchName.length; i++) {
                            if (arrSearchName[i] == $('#report_name').val()) {
                                alert('Search name already exist.');
                                return false;
                            }
                        }
                    }
                }
                document.frm_reports.action = "index.php?showpage=" + optical_showpage;
                document.frm_reports.submit();
            }


            lightBoxFlag = 0;
            function toggle_lightbox(show_hide_flag) {
                show_hide_flag = show_hide_flag || '';
                if (show_hide_flag == 'hide')
                    lightBoxFlag = 1;
                else if (show_hide_flag == 'show')
                    lightBoxFlag = 0;

                var popupid = '#divLightBox';
                if (!lightBoxFlag) {
                    $(popupid).fadeIn();
                    lightBoxFlag = 1;
                } else {
                    $(popupid).fadeOut();
                    lightBoxFlag = 0;
                }
                $('#report_name').val('');
                //$('#divLightBox').append('<div id="fade"></div>');
                //$('#fade').css({'filter':'alpha(opacity=50)'}).fadeIn();
                var popuptopmargin = ($(popupid).height() + 10) / 2;
                var popupleftmargin = ($(popupid).width() + 10) / 2;
                $(popupid).css({
                    'margin-top': -popuptopmargin,
                    'margin-left': -popupleftmargin
                });
            }

            function generate_pdf() {
                if (file_location != '') {
                    top.JS_WEB_ROOT_PATH = '<?php echo $GLOBALS['webroot']; ?>';
                    top.html_to_pdf(file_location, 'l');
                    window.close();
                }
            }



            function enableOrNot(val) {
                /*		if(val=='Detail'){
                 $('#home_facility').attr('disabled', false);
                 }else{
                 $('#home_facility').attr('checked', false);
                 $('#home_facility').attr('disabled', true);
                 }*/
            }

            function addRemoveGroupBy(dateRangeFor) {
                if (dateRangeFor == 'date_of_service') {
                    $("#viewBy").append('<option value="operator">Operator</option>');
                    $('#without_deleted_amounts').attr('disabled', true);
                } else {
                    $("#viewBy option[value='operator']").remove();
                    $('#without_deleted_amounts').attr('disabled', false);
                }
            }

            // SAVED SEARCH FUNCTIONS
            var dChk = 0;
            function callAjaxFile(ddText, opIndex) {
                oDropdown.off("change");
                var returnVal = 0;
                dChk = 1;
                var dd = confirm('Are sure to delete the selected search?');
                if (dd) {
                    $.ajax({
                        url: "delete_search.php?sTxt=" + ddText,
                        success: function (callSts) {
                            if (callSts == '1') {
                                oDropdown.close();
                                oDropdown.remove(opIndex);
                                oDropdown.set("selectedIndex", 0);
                            }
                        }
                    });
                }
                return returnVal;
            }

            function callSavedSearch(srchVal, formId) {
                top.show_loading_image('hide');
                top.show_loading_image('show');

                if (srchVal != '' && dChk != '1') {
                    dChk = 0;

                    $('#call_from_saved').val('yes');
                    $('#' + formId).submit();
                }
                dChk = 0;
            }
            // END SAVED SEARCH	

            function order_status(id, name, status) {
                if (status = true) {
                    document.frm_reports.action = "index.php?showpage=" + optical_showpage + "&confirm_id=" + id + "&name=" + name;
                    document.frm_reports.submit();
                }
            }
           
            function enable_disable_time(ctrlVal) {
                if (ctrlVal == 'transaction_date') {
                    $('#hourFrom').prop('disabled', false);
                    $('#hourTo').prop('disabled', false);
                } else {
                    $('#hourFrom').prop('disabled', true);
                    $('#hourTo').prop('disabled', true);
                }
            }
            $(document).ready(function (e) {

                DateOptions('<?php echo $_POST['dayReport']; ?>');
                enable_disable_time('<?php echo $_POST['DateRangeFor']; ?>');
                getPatientName('<?php echo $_REQUEST['patientId']; ?>');
                function set_container_height() {
                    $('.reportlft').css({
                        'height': (window.innerHeight),
                        'max-height': (window.innerHeight),
                        'overflow-x': 'hidden',
                        'overflowY': 'auto'
                    });
                }

                $(window).load(function () {
                    set_container_height();
                    $('#csvFileDataTable').height($('.reportlft').height() - 70);
                });

                $(window).resize(function () {
                    set_container_height();
                    $('#csvFileDataTable').height($('.reportlft').height() - 70);
                });
                var page_heading = "<?php echo $dbtemp_name; ?>";
                set_header_title(page_heading);


            });

        </script>
    </body>
</html>