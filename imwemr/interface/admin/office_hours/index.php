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
require_once("../admin_header.php");

if (isset($_REQUEST) && isset($_REQUEST['saveFrm']) && $_REQUEST['saveFrm'] == '1') {
    $id = trim($_REQUEST['frm_id']);
    $enable_office_hours = 0;
    if (isset($_REQUEST['enable_office_hours']) && $_REQUEST['enable_office_hours'] != '')
        $enable_office_hours = trim($_REQUEST['enable_office_hours']);

    $weekdays = implode(',', $_REQUEST['weekdays']);
    $start_hour = trim($_REQUEST['start_hour']);
    $start_min = trim($_REQUEST['start_min']);
    $start_time = trim($_REQUEST['start_time']);
    $end_hour = trim($_REQUEST['end_hour']);
    $end_min = trim($_REQUEST['end_min']);
    $end_time = trim($_REQUEST['end_time']);
    $excluded_users = implode(',', $_REQUEST['excluded_users']);
    $operator_id = $_SESSION['authId'];
    $added_on_time = date('Y-m-d H:i:s');

    if ($id == '') {
        $q = 'INSERT INTO office_hours_settings 
                (enable_office_hours,weekdays,start_hour,start_min,start_time,end_hour,end_min,end_time,excluded_users,operator_id,added_on) 
                VALUES ("' . $enable_office_hours . '", "' . $weekdays . '", "' . $start_hour . '", "' . $start_min . '","' . $start_time . '", "' . $end_hour . '","' . $end_min . '", "' . $end_time . '",
                 "' . $excluded_users . '", "' . $operator_id . '", "' . $added_on_time . '") ';
    } else {
        $q = "UPDATE office_hours_settings SET enable_office_hours='" . $enable_office_hours . "', weekdays='" . $weekdays . "', start_hour='" . $start_hour . "', start_min='" . $start_min . "' 
                , start_time='" . $start_time . "', end_hour='" . $end_hour . "' , end_min='" . $end_min . "', end_time='" . $end_time . "' 
                , excluded_users='" . $excluded_users . "', modified_by='" . $operator_id . "',updated_on='" . $added_on_time . "'
                WHERE id='" . $id . "' ";
    }
    imw_query($q);
}

$ofchrsrow = array();
$sqlRes = imw_query("select id,enable_office_hours,weekdays,start_hour,start_min,start_time,end_hour,end_min,end_time,excluded_users from office_hours_settings ");
$ofchrsrow = imw_fetch_assoc($sqlRes);
$id = (isset($ofchrsrow['id']) && $ofchrsrow['id'] != '') ? $ofchrsrow['id'] : '';

$start_time = (isset($ofchrsrow['start_time']) && $ofchrsrow['start_time'] != '') ? $ofchrsrow['start_time'] : 'AM';
$end_time = (isset($ofchrsrow['end_time']) && $ofchrsrow['end_time'] != '') ? $ofchrsrow['end_time'] : '';

$default_exclued=array();
/* Get Users */
$users_arr = array();
$users_str = '';
$sql = "select id,fname,mname,lname,username from `users` where `delete_status` = '0' order by `lname`";
$sql_rs = imw_query($sql);
if ($sql_rs && imw_num_rows($sql_rs) > 0) {
    $users_str = '<select class="selectpicker" multiple name="excluded_users[]" id="excluded_users" data-width="100%" data-size="10" data-title="Select User" data-actions-box="true" data-live-search="true">';
    while ($row = imw_fetch_assoc($sql_rs)) {
        $sel = ( (isset($ofchrsrow['excluded_users']) && in_array($row['id'], explode(',', $ofchrsrow['excluded_users']))) || (isset($row['username']) && in_array($row['username'],$default_exclued)) ) ? ' selected ' : '';
        $prov_name = core_name_format($row['lname'], $row['fname'], $row['mname']);
        $users_arr[$row['id']] = $prov_name;
        $users_str .= '<option "'.$row['username'].'" value="'.$row['id'].'"  "'.$sel.'">' . $prov_name . '</opiton>';
    }
    $users_str .= '</select>';
}

/* Get Users */
$weekdaysArr = array('Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday', 'Sunday' => 'Sunday');
$weekdays_str = '';
foreach ($weekdaysArr as $key => $weekday) {
    $sel = (isset($ofchrsrow['weekdays']) && in_array($key, explode(',', $ofchrsrow['weekdays']))) ? ' selected ' : '';
    $weekdays_str .= '<option value="' . $key . '"  "' . $sel . '">' . $weekday . '</opiton>';
}

//MAKE TIME OPTIONS

$start_hour = (isset($ofchrsrow['start_hour']) && $ofchrsrow['start_hour'] != '') ? $ofchrsrow['start_hour'] : 0;
$hour_option = '';
for ($h = 1; $h <= 12; $h++) {
    if ($h < 10) {
        $h = '0' . $h;
    }
    $hour_sel = $h == $start_hour ? 'selected' : '';
    $hour_option .= '<option value="' . $h . '" ' . $hour_sel . ' >' . $h . '</option>';
}

$start_min = (isset($ofchrsrow['start_min']) && $ofchrsrow['start_min'] != '') ? $ofchrsrow['start_min'] : 0;
$min_option = '';
for ($m = 0; $m < 60; $m++) {
    if ($m < 10) {
        $m = '0' . $m;
    }
    $min_sel = $m == $start_min ? 'selected' : '';
    $min_option .= '<option value="' . $m . '" ' . $min_sel . ' >' . $m . '</option>';
}

$end_hour = (isset($ofchrsrow['end_hour']) && $ofchrsrow['end_hour'] != '') ? $ofchrsrow['end_hour'] : 0;
$end_hour_option = '';
for ($h = 1; $h <= 12; $h++) {
    if ($h < 10) {
        $h = '0' . $h;
    }
    $end_hour_sel = $h == $end_hour ? 'selected' : '';
    $end_hour_option .= '<option value="' . $h . '" ' . $end_hour_sel . ' >' . $h . '</option>';
}


$end_min = (isset($ofchrsrow['end_min']) && $ofchrsrow['end_min'] != '') ? $ofchrsrow['end_min'] : 0;
$end_min_option = '';
for ($m = 0; $m < 60; $m++) {
    if ($m < 10) {
        $m = '0' . $m;
    }
    $end_min_sel = $m == $end_min ? 'selected' : '';
    $end_min_option .= '<option value="' . $m . '" ' . $end_min_sel . ' >' . $m . '</option>';
}


?>

<body>
    <div class="container-fluid">
        <div class="whtbox" >
            <div class="">
                <div class="head">
                    <span>Enable Office Hours Settings</span>
                </div>
                <form name="offHrsFrm" id="offHrsFrm" method="POST" action="../office_hours/index.php">
                    <div class="office_settings_box">
                        <div class="row">
                            <div class="table-responsive respotable adminnw pd5">
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <?php
                                        $checked = '';
                                        $enable_office_hours = 0;
                                        if (isset($ofchrsrow['enable_office_hours']) && $ofchrsrow['enable_office_hours'] == 1) {
                                            $checked = 'checked';
                                            $enable_office_hours = $ofchrsrow['enable_office_hours'];
                                        }
                                        ?>
                                        <input type="checkbox" name="enable_office_hours" id="enable_office_hours" <?php echo $checked; ?> value="<?php echo $enable_office_hours; ?>" onchange="enableOfficeHoursSettings();" autocomplete="off">
                                        <label for="enable_office_hours">Enable Login Hours</label>
                                    </div>
                                </div>
                                <div class="col-sm-8 form-inline">
                                </div>
                            </div>

                            <div class="hours_box pd5">
                                <div class="col-sm-2">
                                    <label for="weekdays">Week Day</label>
                                    <select class="selectpicker" multiple name="weekdays[]" id="weekdays" data-width="100%" data-size="10" data-title="Weeks Day" data-actions-box="true" data-live-search="true">
                                        <?php echo $weekdays_str; ?>  
                                    </select>
                                </div>
                                <div class="col-sm-4">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                        <label>Hours From</label>
                                                        <select id="start_hour" name="start_hour" class="form-control minimal selecicon">
                                                            <option value="" >--</option>
                                                            <?php echo $hour_option; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                        <label>Min. From</label>
                                                        <select id="start_min" name="start_min" class="form-control minimal selecicon">
                                                            <option value="" >--</option>
                                                            <?php echo $min_option; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                        <label>&nbsp;</label>
                                                        <select id="start_time" name="start_time" class="form-control minimal selecicon">
                                                            <option <?php echo ($start_time == "AM") ? 'selected' : ''; ?> value="AM">AM</option>
                                                            <option <?php echo ($start_time == "PM") ? 'selected' : ''; ?> value="PM">PM</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6 col-md-6 col-sm-6">
                                            <div class="form-group">
                                                <div class="row">
                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                        <label>Hours To</label>    
                                                        <select id="end_hour" name="end_hour" class="form-control minimal selecicon">
                                                            <option value="" >--</option>
                                                            <?php echo $end_hour_option; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                        <label>Min. To</label>
                                                        <select id="end_min" name="end_min" class="form-control minimal selecicon">
                                                            <option value="" >--</option>
                                                            <?php echo $end_min_option; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-lg-4 col-md-4 col-sm-4">
                                                        <label>&nbsp;</label>
                                                        <select id="end_time" name="end_time" class="form-control minimal selecicon">
                                                            <option value="">--</option>
                                                            <option <?php echo ($end_time == "AM") ? 'selected' : ''; ?> value="AM">AM</option>
                                                            <option <?php echo ($end_time == "PM") ? 'selected' : ''; ?> value="PM">PM</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="col-sm-2">
                                    <label for="hourfrom">Hour From</label>
                                    <select name="hourfrom" id="hourfrom" class="selectpicker " data-size="10"  title="Select Hour From">
                                <?php echo $timeHourFromOptions; ?>    
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label for="hourto">Hour To</label>
                                <select name="hourto" id="hourto" class="selectpicker " data-size="10"  title="Select Hour To">
                                <?php echo $timeHourToOptions; ?>
                                </select>
                            </div>-->
                                <!--<div class="col-sm-2">
                                    <label for="time_zone">Time Zone</label>
                                    <select name="time_zone" id="time_zone" class="selectpicker " data-size="10"  title="Select Time Zone">
                                        <option value="Monday">Monday</option>
                                        <option value="Tuesday">Tuesday</option>
                                        <option value="Wednesday">Wednesday</option>
                                        <option value="Thursday">Thursday</option>
                                    </select>
                                </div>-->
                                <div class="col-sm-2">
                                    <label for="excluded_users">Exclude Users</label>
                                    <?php echo $users_str; ?>
                                </div>
                                <div class="col-sm-4 form-inline">
                                    <input type="hidden" name="saveFrm" id="saveFrm" value="0"/>
                                    <input type="hidden" name="frm_id" id="frm_id" value="<?php echo $id; ?>"/>
                                </div>
                            </div>
                        </div>	
                    </div>

                </form>
                
            </div>
            <div class="clearfix"></div>
            
            <!-- Report -->
            <?php if(empty($ofchrsrow)==false && (isset($ofchrsrow['enable_office_hours']) && $ofchrsrow['enable_office_hours'] == 1)  ) {
                $start_hour=$ofchrsrow['start_hour'];
                $start_min=$ofchrsrow['start_min'];
                $start_time=$ofchrsrow['start_time'];

                $end_hour=$ofchrsrow['end_hour'];
                $end_min=$ofchrsrow['end_min'];
                $end_time=$ofchrsrow['end_time'];

                $office_start_time=$start_hour.':'.$start_min.' '.$start_time;
                $office_end_time=$end_hour.':'.$end_min.' '.$end_time;
                $excluded_users=explode(',',$ofchrsrow['excluded_users']);
                $ex_users_str='';
                foreach($excluded_users as $uitem) {
                    $ex_users_str.=$users_arr[$uitem].'; ';
                }
                ?>
                <div class="row">
                    <div class="col-sm-12 pd5" style="padding-top:50px;padding-left:20px;padding-right:20px;min-height:250px;">
                        <table class="table table-bordered table-hover adminnw" id="table_color">
                            <thead>
                                <tr>
                                    <th width="30%">Weekdays</th>
                                    <th width="10%">Start Time</th>
                                    <th width="10%">End Time</th>
                                    <th width="50%">Excluded Users</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td width="30%"><?php echo $ofchrsrow['weekdays'];?></td>
                                    <td width="10%"><?php echo $office_start_time;?></td>
                                    <td width="10%"><?php echo $office_end_time;?></td>
                                    <td width="50%"><?php echo $ex_users_str;?></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="clearfix"></div>
                    </div>
                    
                    <p class="text-center">This setting enables office hours for all users except excluded users for selected week days.</p>
                </div>
            <?php } ?>
        </div>
    </div>

<script>
    function enableOfficeHoursSettings() {
        if ($("#enable_office_hours").is(':checked') == true) {
            $("#enable_office_hours").val('1');
            $('.hours_box').find('select').prop('disabled', false);
        } else {
            $("#enable_office_hours").val('0');
            $('.hours_box').find('select').prop('disabled', true);
        }
        $('.selectpicker').selectpicker('refresh');
    }

    function save_office_settings() {
        $("#saveFrm").val('1');
        var alert_msg = '';
        if ($("#enable_office_hours").is(':checked') == true) {

            if ($('#weekdays').find("option:selected").length == 0) {
                alert_msg += 'Please select Week Day.';
            }
            if ($("#start_hour option:selected").val() == '' || $("#start_min option:selected").val() == '' || $("#start_time option:selected").val() == '') {
                alert_msg += '<br>Please select Time From.';
            }
            if ($("#end_hour option:selected").val() == '' || $("#end_min option:selected").val() == '' || $("#end_time option:selected").val() == '') {
                alert_msg += '<br>Please select Time To.';
            }
            var office_start_time='';
            var office_end_time='';
            if ($("#start_hour option:selected").val() != '' && $("#start_min option:selected").val() != '' && $("#start_time option:selected").val() != ''
                    && $("#end_hour option:selected").val() != '' && $("#end_min option:selected").val() != '' && $("#end_time option:selected").val() != ''
                    ) {
                office_start_time=$("#start_hour option:selected").val()+':'+$("#start_min option:selected").val()+' '+$("#start_time option:selected").val();
                office_end_time=$("#end_hour option:selected").val()+':'+$("#end_min option:selected").val()+' '+$("#end_time option:selected").val();
                
                var today = new Date();
                var dt = today.getDate();
                var mnth = today.getMonth()+1; //January is 0!
                var yrs = today.getFullYear();
                
                office_start_time=new Date(yrs+'/'+mnth+'/'+dt+' '+office_start_time).getTime();
                office_end_time=new Date(yrs+'/'+mnth+'/'+dt+' '+office_end_time).getTime();

            }

            if(office_start_time!='' && office_end_time!='' && !(office_end_time > office_start_time)){
                alert_msg += '<br>End time must be greater than start time.';
            }
            
            if ($('#excluded_users').find("option:selected").length == 0) {
                alert_msg += '<br>Please exclude dev and helpdesk users.';
            }
        }
        if (alert_msg != '') {
            top.fAlert(alert_msg);
            return false;
        }

        $('#offHrsFrm').submit();
    }

    $(document).ready(function () {
        set_header_title('Login Office Hours Setting');
        top.show_loading_image('hide');
        enableOfficeHoursSettings();
        $('.selectpicker').selectpicker('refresh');
    });
    $(window).load(function () {
        var ar_btn = [["saveoffcsetting", "Done", "top.fmain.save_office_settings();"]];

        top.btn_show("ADMN", ar_btn);

        parent.show_loading_image('none');
    });

</script>
<?php require_once("../admin_footer.php"); ?> 
